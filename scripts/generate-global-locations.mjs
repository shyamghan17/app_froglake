import fs from 'node:fs';
import path from 'node:path';
import https from 'node:https';
import zlib from 'node:zlib';
import { pipeline } from 'node:stream/promises';

const DATA_URL =
    'https://github.com/dr5hn/countries-states-cities-database/releases/latest/download/json-countries+states+cities.json.gz';

const projectRoot = path.resolve(import.meta.dirname, '..');
const outputPath = path.join(projectRoot, 'resources/js/data/locations/global.json');
const tmpPath = path.join(projectRoot, '.dbg', 'csc-countries+states+cities.json.gz');

const normalizeKey = (value) => String(value ?? '').trim().toLowerCase();

const download = async (url, destPath) => {
    await fs.promises.mkdir(path.dirname(destPath), { recursive: true });

    return new Promise((resolve, reject) => {
        const request = https.get(url, (response) => {
            if (response.statusCode && response.statusCode >= 300 && response.statusCode < 400 && response.headers.location) {
                resolve(download(response.headers.location, destPath));
                return;
            }

            if (!response.statusCode || response.statusCode < 200 || response.statusCode >= 300) {
                reject(new Error(`Download failed: ${response.statusCode ?? 'unknown'} ${url}`));
                return;
            }

            const fileStream = fs.createWriteStream(destPath);
            pipeline(response, fileStream).then(resolve).catch(reject);
        });

        request.on('error', reject);
    });
};

const dedupePreserveCaseSorted = (values) => {
    const map = new Map();
    for (const value of values) {
        const name = String(value ?? '').trim();
        if (!name) continue;
        const key = normalizeKey(name);
        if (!map.has(key)) {
            map.set(key, name);
        }
    }

    return [...map.values()].sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
};

const main = async () => {
    await download(DATA_URL, tmpPath);
    try {
        const gz = await fs.promises.readFile(tmpPath);
        const jsonText = zlib.gunzipSync(gz).toString('utf8');
        const countries = JSON.parse(jsonText);

        const mapped = {
            countries: (countries ?? [])
                .map((country) => {
                    const countryName = String(country?.name ?? '').trim();
                    if (!countryName) return undefined;

                    const states = (country?.states ?? [])
                        .map((state) => {
                            const stateName = String(state?.name ?? '').trim();
                            if (!stateName) return undefined;

                            const cities = dedupePreserveCaseSorted((state?.cities ?? []).map((city) => city?.name));
                            return { name: stateName, cities };
                        })
                        .filter(Boolean)
                        .sort((a, b) => a.name.localeCompare(b.name, undefined, { sensitivity: 'base' }));

                    return { name: countryName, states };
                })
                .filter(Boolean)
                .sort((a, b) => a.name.localeCompare(b.name, undefined, { sensitivity: 'base' })),
        };

        await fs.promises.writeFile(outputPath, `${JSON.stringify(mapped, null, 4)}\n`, 'utf8');

        const countryCount = mapped.countries.length;
        const stateCount = mapped.countries.reduce((sum, country) => sum + country.states.length, 0);
        const cityCount = mapped.countries.reduce(
            (sum, country) => sum + country.states.reduce((inner, state) => inner + state.cities.length, 0),
            0,
        );

        process.stdout.write(
            `Wrote ${countryCount} countries, ${stateCount} states, ${cityCount} cities to ${path.relative(projectRoot, outputPath)}\n`,
        );
    } finally {
        await fs.promises.unlink(tmpPath).catch(() => undefined);
    }
};

await main();

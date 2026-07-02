import locationData from '@/data/locations/global.json';

export type LocationCountry = {
    name: string;
    states: Array<{
        name: string;
        cities: string[];
    }>;
};

type LocationDataset = {
    countries: LocationCountry[];
};

const dataset = locationData as LocationDataset;

const normalize = (value: string) => value.trim().toLowerCase();

const withCurrentOption = (options: string[], currentValue?: string) => {
    const current = (currentValue ?? '').trim();
    if (!current) {
        return options;
    }

    const has = options.some((option) => normalize(option) === normalize(current));
    return has ? options : [current, ...options];
};

const findCountry = (countryName?: string) => {
    const name = (countryName ?? '').trim();
    if (!name) {
        return undefined;
    }

    const needle = normalize(name);
    return dataset.countries.find((country) => normalize(country.name) === needle);
};

const findState = (country: LocationCountry | undefined, stateName?: string) => {
    const name = (stateName ?? '').trim();
    if (!country || !name) {
        return undefined;
    }

    const needle = normalize(name);
    return country.states.find((state) => normalize(state.name) === needle);
};

export const getCountryOptions = (currentCountry?: string) => {
    const countries = dataset.countries.map((country) => country.name);
    return withCurrentOption(countries, currentCountry);
};

export const getStateOptions = (countryName?: string, currentState?: string) => {
    const country = findCountry(countryName);
    const states = country ? country.states.map((state) => state.name) : [];
    return withCurrentOption(states, currentState);
};

export const getCityOptions = (countryName?: string, stateName?: string, currentCity?: string) => {
    const country = findCountry(countryName);
    const state = findState(country, stateName);
    const cities = state ? state.cities : [];
    return withCurrentOption(cities, currentCity);
};

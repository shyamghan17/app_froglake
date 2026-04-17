<?php

namespace Workdo\FindGoogleLeads\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\FindGoogleLeads\Models\FindGoogleLeadFoundedLead;
use Workdo\FindGoogleLeads\Models\FindGoogleLeadFoundedLeadContact;
use Carbon\Carbon;

class DemoFindGoogleLeadsSeeder extends Seeder
{
    public function run($userId): void
    {
        if (!empty($userId)) {
            if (FindGoogleLeadFoundedLead::where('created_by', $userId)->exists() || FindGoogleLeadFoundedLeadContact::where('created_by', $userId)->exists()) {
                return;
            }

            Model::unguard();

            $leadSearchData = [
                ['name' => 'Digital Marketing Agencies', 'keywords' => 'digital marketing agency', 'address' => 'New York, NY, USA', 'created_at' => Carbon::now()->subDays(179)],
                ['name' => 'Web Development Companies', 'keywords' => 'web development company', 'address' => 'San Francisco, CA, USA', 'created_at' => Carbon::now()->subDays(173)],
                ['name' => 'Software Development Firms', 'keywords' => 'software development', 'address' => 'Austin, TX, USA', 'created_at' => Carbon::now()->subDays(167)],
                ['name' => 'IT Consulting Services', 'keywords' => 'IT consulting', 'address' => 'Seattle, WA, USA', 'created_at' => Carbon::now()->subDays(161)],
                ['name' => 'Mobile App Developers', 'keywords' => 'mobile app development', 'address' => 'Los Angeles, CA, USA', 'created_at' => Carbon::now()->subDays(155)],
                ['name' => 'E-commerce Solutions', 'keywords' => 'ecommerce development', 'address' => 'Chicago, IL, USA', 'created_at' => Carbon::now()->subDays(149)],
                ['name' => 'Cloud Computing Services', 'keywords' => 'cloud computing', 'address' => 'Boston, MA, USA', 'created_at' => Carbon::now()->subDays(143)],
                ['name' => 'Cybersecurity Companies', 'keywords' => 'cybersecurity services', 'address' => 'Washington, DC, USA', 'created_at' => Carbon::now()->subDays(137)],
                ['name' => 'Data Analytics Firms', 'keywords' => 'data analytics', 'address' => 'Denver, CO, USA', 'created_at' => Carbon::now()->subDays(131)],
                ['name' => 'AI Development Companies', 'keywords' => 'artificial intelligence', 'address' => 'San Jose, CA, USA', 'created_at' => Carbon::now()->subDays(125)],
                ['name' => 'Blockchain Developers', 'keywords' => 'blockchain development', 'address' => 'Miami, FL, USA', 'created_at' => Carbon::now()->subDays(119)],
                ['name' => 'UX/UI Design Agencies', 'keywords' => 'UX UI design', 'address' => 'Portland, OR, USA', 'created_at' => Carbon::now()->subDays(113)],
                ['name' => 'DevOps Consulting', 'keywords' => 'DevOps consulting', 'address' => 'Atlanta, GA, USA', 'created_at' => Carbon::now()->subDays(107)],
                ['name' => 'SaaS Development', 'keywords' => 'SaaS development', 'address' => 'Phoenix, AZ, USA', 'created_at' => Carbon::now()->subDays(101)],
                ['name' => 'Healthcare IT Solutions', 'keywords' => 'healthcare IT', 'address' => 'Houston, TX, USA', 'created_at' => Carbon::now()->subDays(95)],
                ['name' => 'FinTech Development', 'keywords' => 'fintech development', 'address' => 'Charlotte, NC, USA', 'created_at' => Carbon::now()->subDays(89)],
                ['name' => 'EdTech Solutions', 'keywords' => 'educational technology', 'address' => 'Raleigh, NC, USA', 'created_at' => Carbon::now()->subDays(83)],
                ['name' => 'IoT Development', 'keywords' => 'IoT development', 'address' => 'San Diego, CA, USA', 'created_at' => Carbon::now()->subDays(77)],
                ['name' => 'AR/VR Development', 'keywords' => 'augmented reality', 'address' => 'Las Vegas, NV, USA', 'created_at' => Carbon::now()->subDays(71)],
                ['name' => 'Machine Learning Services', 'keywords' => 'machine learning', 'address' => 'Nashville, TN, USA', 'created_at' => Carbon::now()->subDays(65)],
                ['name' => 'API Development', 'keywords' => 'API development', 'address' => 'Minneapolis, MN, USA', 'created_at' => Carbon::now()->subDays(59)],
                ['name' => 'Database Consulting', 'keywords' => 'database consulting', 'address' => 'Kansas City, MO, USA', 'created_at' => Carbon::now()->subDays(53)],
                ['name' => 'Quality Assurance Services', 'keywords' => 'software testing', 'address' => 'Salt Lake City, UT, USA', 'created_at' => Carbon::now()->subDays(47)],
                ['name' => 'Business Intelligence', 'keywords' => 'business intelligence', 'address' => 'Tampa, FL, USA', 'created_at' => Carbon::now()->subDays(41)],
                ['name' => 'CRM Development', 'keywords' => 'CRM development', 'address' => 'Orlando, FL, USA', 'created_at' => Carbon::now()->subDays(35)],
                ['name' => 'ERP Solutions', 'keywords' => 'ERP solutions', 'address' => 'Columbus, OH, USA', 'created_at' => Carbon::now()->subDays(29)],
                ['name' => 'Digital Transformation', 'keywords' => 'digital transformation', 'address' => 'Indianapolis, IN, USA', 'created_at' => Carbon::now()->subDays(23)],
                ['name' => 'Automation Services', 'keywords' => 'business automation', 'address' => 'Milwaukee, WI, USA', 'created_at' => Carbon::now()->subDays(17)],
                ['name' => 'Integration Services', 'keywords' => 'system integration', 'address' => 'Richmond, VA, USA', 'created_at' => Carbon::now()->subDays(11)],
                ['name' => 'Tech Startups', 'keywords' => 'technology startup', 'address' => 'Austin, TX, USA', 'created_at' => Carbon::now()->subDays(5)]
            ];

            $contactsData = [
                // Digital Marketing Agencies contacts
                [
                    ['name' => 'TechFlow Digital', 'email' => 'contact@techflowdigital.com', 'mobile_no' => '+919876543210', 'website' => 'https://techflowdigital.com', 'address' => '123 Broadway, New York, NY 10001'],
                    ['name' => 'Creative Minds Agency', 'email' => 'hello@creativeminds.com', 'mobile_no' => '+919876543211', 'website' => 'https://creativeminds.com', 'address' => '456 Madison Ave, New York, NY 10022'],
                    ['name' => 'Digital Boost Marketing', 'email' => 'info@digitalboost.com', 'mobile_no' => '+919876543212', 'website' => 'https://digitalboost.com', 'address' => '789 5th Avenue, New York, NY 10019'],
                    ['name' => 'NextGen Marketing', 'email' => 'team@nextgenmarketing.com', 'mobile_no' => '+919876543213', 'website' => 'https://nextgenmarketing.com', 'address' => '321 Park Ave, New York, NY 10010']
                ],
                // Web Development Companies contacts
                [
                    ['name' => 'CodeCraft Solutions', 'email' => 'hello@codecraft.com', 'mobile_no' => '+9198765443214', 'website' => 'https://codecraft.com', 'address' => '100 Market St, San Francisco, CA 94105'],
                    ['name' => 'WebForge Studios', 'email' => 'contact@webforge.com', 'mobile_no' => '+9198765443215', 'website' => 'https://webforge.com', 'address' => '200 Mission St, San Francisco, CA 94105'],
                    ['name' => 'Pixel Perfect Dev', 'email' => 'info@pixelperfect.com', 'mobile_no' => '+9198765443216', 'website' => 'https://pixelperfect.com', 'address' => '300 California St, San Francisco, CA 94104'],
                    ['name' => 'Bay Area Web Co', 'email' => 'team@bayareaweb.com', 'mobile_no' => '+9198765443217', 'website' => 'https://bayareaweb.com', 'address' => '400 Montgomery St, San Francisco, CA 94104']
                ],
                // Software Development Firms contacts
                [
                    ['name' => 'Austin Software Labs', 'email' => 'contact@austinlabs.com', 'mobile_no' => '+9198765443218', 'website' => 'https://austinlabs.com', 'address' => '500 Congress Ave, Austin, TX 78701'],
                    ['name' => 'Lone Star Development', 'email' => 'hello@lonestardev.com', 'mobile_no' => '+9198765443219', 'website' => 'https://lonestardev.com', 'address' => '600 Guadalupe St, Austin, TX 78701'],
                    ['name' => 'Texas Tech Solutions', 'email' => 'info@texastech.com', 'mobile_no' => '+9198765443220', 'website' => 'https://texastech.com', 'address' => '700 Brazos St, Austin, TX 78701']
                ],
                // IT Consulting Services contacts
                [
                    ['name' => 'Seattle IT Consultants', 'email' => 'contact@seattleit.com', 'mobile_no' => '+9198765443221', 'website' => 'https://seattleit.com', 'address' => '800 Pine St, Seattle, WA 98101'],
                    ['name' => 'Pacific Northwest Tech', 'email' => 'hello@pnwtech.com', 'mobile_no' => '+9198765443222', 'website' => 'https://pnwtech.com', 'address' => '900 1st Ave, Seattle, WA 98104'],
                    ['name' => 'Emerald City Consulting', 'email' => 'info@emeraldcity.com', 'mobile_no' => '+9198765443223', 'website' => 'https://emeraldcity.com', 'address' => '1000 2nd Ave, Seattle, WA 98104'],
                    ['name' => 'Northwest Digital', 'email' => 'team@nwdigital.com', 'mobile_no' => '+9198765443224', 'website' => 'https://nwdigital.com', 'address' => '1100 3rd Ave, Seattle, WA 98101']
                ],
                // Mobile App Developers contacts
                [
                    ['name' => 'LA Mobile Studios', 'email' => 'contact@lamobile.com', 'mobile_no' => '+9198765443225', 'website' => 'https://lamobile.com', 'address' => '1200 Wilshire Blvd, Los Angeles, CA 90017'],
                    ['name' => 'Hollywood App Co', 'email' => 'hello@hollywoodapp.com', 'mobile_no' => '+9198765443226', 'website' => 'https://hollywoodapp.com', 'address' => '1300 Sunset Blvd, Los Angeles, CA 90026'],
                    ['name' => 'California Mobile Dev', 'email' => 'info@camobiledev.com', 'mobile_no' => '+9198765443227', 'website' => 'https://camobiledev.com', 'address' => '1400 Santa Monica Blvd, Los Angeles, CA 90025']
                ],
                // E-commerce Solutions contacts
                [
                    ['name' => 'Chicago E-commerce Hub', 'email' => 'contact@chicagoecom.com', 'mobile_no' => '+9198765443228', 'website' => 'https://chicagoecom.com', 'address' => '1500 N Michigan Ave, Chicago, IL 60611'],
                    ['name' => 'Windy City Digital', 'email' => 'hello@windycitydigital.com', 'mobile_no' => '+9198765443229', 'website' => 'https://windycitydigital.com', 'address' => '1600 W Lake St, Chicago, IL 60612'],
                    ['name' => 'Midwest E-solutions', 'email' => 'info@midwestesolutions.com', 'mobile_no' => '+9198765443230', 'website' => 'https://midwestesolutions.com', 'address' => '1700 N State St, Chicago, IL 60614']
                ],
                // Cloud Computing Services contacts
                [
                    ['name' => 'Boston Cloud Services', 'email' => 'contact@bostoncloud.com', 'mobile_no' => '+9198765443231', 'website' => 'https://bostoncloud.com', 'address' => '1800 Boylston St, Boston, MA 02199'],
                    ['name' => 'New England Tech', 'email' => 'hello@newenglandtech.com', 'mobile_no' => '+9198765443232', 'website' => 'https://newenglandtech.com', 'address' => '1900 Commonwealth Ave, Boston, MA 02215'],
                    ['name' => 'Atlantic Cloud Solutions', 'email' => 'info@atlanticcloud.com', 'mobile_no' => '+9198765443233', 'website' => 'https://atlanticcloud.com', 'address' => '2000 Beacon St, Boston, MA 02135']
                ],
                // Cybersecurity Companies contacts
                [
                    ['name' => 'Capitol Security Group', 'email' => 'contact@capitolsecurity.com', 'mobile_no' => '+9198765443234', 'website' => 'https://capitolsecurity.com', 'address' => '2100 K St NW, Washington, DC 20037'],
                    ['name' => 'DC Cyber Defense', 'email' => 'hello@dccyberdefense.com', 'mobile_no' => '+9198765443235', 'website' => 'https://dccyberdefense.com', 'address' => '2200 M St NW, Washington, DC 20037'],
                    ['name' => 'Federal Security Solutions', 'email' => 'info@federalsecurity.com', 'mobile_no' => '+9198765443236', 'website' => 'https://federalsecurity.com', 'address' => '2300 L St NW, Washington, DC 20037']
                ],
                // Data Analytics Firms contacts
                [
                    ['name' => 'Rocky Mountain Analytics', 'email' => 'contact@rmanalytics.com', 'mobile_no' => '+9198765443237', 'website' => 'https://rmanalytics.com', 'address' => '2400 17th St, Denver, CO 80202'],
                    ['name' => 'Denver Data Labs', 'email' => 'hello@denverdatalabs.com', 'mobile_no' => '+9198765443238', 'website' => 'https://denverdatalabs.com', 'address' => '2500 Larimer St, Denver, CO 80205'],
                    ['name' => 'Colorado Insights', 'email' => 'info@coloradoinsights.com', 'mobile_no' => '+9198765443239', 'website' => 'https://coloradoinsights.com', 'address' => '2600 Blake St, Denver, CO 80205']
                ],
                // AI Development Companies contacts
                [
                    ['name' => 'Silicon Valley AI', 'email' => 'contact@svai.com', 'mobile_no' => '+9198765443240', 'website' => 'https://svai.com', 'address' => '2700 N 1st St, San Jose, CA 95134'],
                    ['name' => 'Neural Networks Inc', 'email' => 'hello@neuralnetworks.com', 'mobile_no' => '+9198765443241', 'website' => 'https://neuralnetworks.com', 'address' => '2800 Zanker Rd, San Jose, CA 95134'],
                    ['name' => 'AI Innovations Lab', 'email' => 'info@aiinnovations.com', 'mobile_no' => '+9198765443242', 'website' => 'https://aiinnovations.com', 'address' => '2900 Tasman Dr, San Jose, CA 95134']
                ],
                // Blockchain Developers contacts
                [
                    ['name' => 'Miami Blockchain Hub', 'email' => 'contact@miamiblockchain.com', 'mobile_no' => '+9198765443243', 'website' => 'https://miamiblockchain.com', 'address' => '3000 Biscayne Blvd, Miami, FL 33137'],
                    ['name' => 'South Beach Crypto', 'email' => 'hello@southbeachcrypto.com', 'mobile_no' => '+9198765443244', 'website' => 'https://southbeachcrypto.com', 'address' => '3100 Collins Ave, Miami, FL 33140'],
                    ['name' => 'Florida Blockchain Solutions', 'email' => 'info@flblockchain.com', 'mobile_no' => '+9198765443245', 'website' => 'https://flblockchain.com', 'address' => '3200 NE 2nd Ave, Miami, FL 33137']
                ],
                // UX/UI Design Agencies contacts
                [
                    ['name' => 'Portland Design Studio', 'email' => 'contact@portlanddesign.com', 'mobile_no' => '+9198765443246', 'website' => 'https://portlanddesign.com', 'address' => '3300 NW 23rd Ave, Portland, OR 97210'],
                    ['name' => 'Pacific Design Co', 'email' => 'hello@pacificdesign.com', 'mobile_no' => '+9198765443247', 'website' => 'https://pacificdesign.com', 'address' => '3400 SE Hawthorne Blvd, Portland, OR 97214'],
                    ['name' => 'Oregon Creative Labs', 'email' => 'info@oregoncreative.com', 'mobile_no' => '+9198765443248', 'website' => 'https://oregoncreative.com', 'address' => '3500 N Williams Ave, Portland, OR 97227']
                ],
                // DevOps Consulting contacts
                [
                    ['name' => 'Atlanta DevOps Group', 'email' => 'contact@atlantadevops.com', 'mobile_no' => '+9198765443249', 'website' => 'https://atlantadevops.com', 'address' => '3600 Peachtree Rd NE, Atlanta, GA 30326'],
                    ['name' => 'Southern DevOps Solutions', 'email' => 'hello@southerndevops.com', 'mobile_no' => '+9198765443250', 'website' => 'https://southerndevops.com', 'address' => '3700 Piedmont Rd NE, Atlanta, GA 30305'],
                    ['name' => 'Georgia Tech Consulting', 'email' => 'info@gatechconsulting.com', 'mobile_no' => '+9198765443251', 'website' => 'https://gatechconsulting.com', 'address' => '3800 Spring St NW, Atlanta, GA 30309']
                ],
                // SaaS Development contacts
                [
                    ['name' => 'Phoenix SaaS Labs', 'email' => 'contact@phoenixsaas.com', 'mobile_no' => '+9198765443252', 'website' => 'https://phoenixsaas.com', 'address' => '3900 N Central Ave, Phoenix, AZ 85012'],
                    ['name' => 'Desert Software Solutions', 'email' => 'hello@desertsoftware.com', 'mobile_no' => '+9198765443253', 'website' => 'https://desertsoftware.com', 'address' => '4000 E Camelback Rd, Phoenix, AZ 85018'],
                    ['name' => 'Arizona Cloud Services', 'email' => 'info@azcloudservices.com', 'mobile_no' => '+9198765443254', 'website' => 'https://azcloudservices.com', 'address' => '4100 N Scottsdale Rd, Phoenix, AZ 85251']
                ],
                // Healthcare IT Solutions contacts
                [
                    ['name' => 'Houston Health Tech', 'email' => 'contact@houstonhealthtech.com', 'mobile_no' => '+9198765443255', 'website' => 'https://houstonhealthtech.com', 'address' => '4200 Main St, Houston, TX 77002'],
                    ['name' => 'Texas Medical IT', 'email' => 'hello@texasmedicalit.com', 'mobile_no' => '+9198765443256', 'website' => 'https://texasmedicalit.com', 'address' => '4300 Fannin St, Houston, TX 77004'],
                    ['name' => 'Gulf Coast Healthcare Solutions', 'email' => 'info@gulfcoasthealthcare.com', 'mobile_no' => '+9198765443257', 'website' => 'https://gulfcoasthealthcare.com', 'address' => '4400 Richmond Ave, Houston, TX 77027']
                ],
                // FinTech Development contacts
                [
                    ['name' => 'Charlotte FinTech Hub', 'email' => 'contact@charlottefintech.com', 'mobile_no' => '+9198765443258', 'website' => 'https://charlottefintech.com', 'address' => '4500 S Tryon St, Charlotte, NC 28217'],
                    ['name' => 'Carolina Financial Tech', 'email' => 'hello@carolinafintech.com', 'mobile_no' => '+9198765443259', 'website' => 'https://carolinafintech.com', 'address' => '4600 Park Rd, Charlotte, NC 28209'],
                    ['name' => 'Queen City Digital Finance', 'email' => 'info@queencityfinance.com', 'mobile_no' => '+9198765443260', 'website' => 'https://queencityfinance.com', 'address' => '4700 Independence Blvd, Charlotte, NC 28205']
                ],
                // EdTech Solutions contacts
                [
                    ['name' => 'Raleigh EdTech Innovations', 'email' => 'contact@raleighedtech.com', 'mobile_no' => '+9198765443261', 'website' => 'https://raleighedtech.com', 'address' => '4800 Glenwood Ave, Raleigh, NC 27603'],
                    ['name' => 'Triangle Learning Solutions', 'email' => 'hello@trianglelearning.com', 'mobile_no' => '+9198765443262', 'website' => 'https://trianglelearning.com', 'address' => '4900 Capital Blvd, Raleigh, NC 27604'],
                    ['name' => 'NC Educational Technology', 'email' => 'info@ncedtech.com', 'mobile_no' => '+9198765443263', 'website' => 'https://ncedtech.com', 'address' => '5000 Falls of Neuse Rd, Raleigh, NC 27609']
                ],
                // IoT Development contacts
                [
                    ['name' => 'San Diego IoT Labs', 'email' => 'contact@sdiotlabs.com', 'mobile_no' => '+9198765443264', 'website' => 'https://sdiotlabs.com', 'address' => '5100 Pacific Hwy, San Diego, CA 92110'],
                    ['name' => 'Southern California IoT', 'email' => 'hello@socaliiot.com', 'mobile_no' => '+9198765443265', 'website' => 'https://socaliiot.com', 'address' => '5200 Balboa Ave, San Diego, CA 92111'],
                    ['name' => 'Coastal Connected Devices', 'email' => 'info@coastalconnected.com', 'mobile_no' => '+9198765443266', 'website' => 'https://coastalconnected.com', 'address' => '5300 Clairemont Mesa Blvd, San Diego, CA 92117']
                ],
                // AR/VR Development contacts
                [
                    ['name' => 'Vegas Virtual Reality', 'email' => 'contact@vegasvr.com', 'mobile_no' => '+9198765443267', 'website' => 'https://vegasvr.com', 'address' => '5400 Las Vegas Blvd S, Las Vegas, NV 89119'],
                    ['name' => 'Nevada Immersive Tech', 'email' => 'hello@nevadaimmersive.com', 'mobile_no' => '+9198765443268', 'website' => 'https://nevadaimmersive.com', 'address' => '5500 W Sahara Ave, Las Vegas, NV 89146'],
                    ['name' => 'Desert AR Solutions', 'email' => 'info@desertarsolutions.com', 'mobile_no' => '+9198765443269', 'website' => 'https://desertarsolutions.com', 'address' => '5600 S Eastern Ave, Las Vegas, NV 89119']
                ],
                // Machine Learning Services contacts
                [
                    ['name' => 'Nashville ML Labs', 'email' => 'contact@nashvilleml.com', 'mobile_no' => '+9198765443270', 'website' => 'https://nashvilleml.com', 'address' => '5700 Charlotte Ave, Nashville, TN 37209'],
                    ['name' => 'Music City AI', 'email' => 'hello@musiccityai.com', 'mobile_no' => '+9198765443271', 'website' => 'https://musiccityai.com', 'address' => '5800 Broadway, Nashville, TN 37203'],
                    ['name' => 'Tennessee Tech Solutions', 'email' => 'info@tntech.com', 'mobile_no' => '+9198765443272', 'website' => 'https://tntech.com', 'address' => '5900 West End Ave, Nashville, TN 37205']
                ],
                // API Development contacts
                [
                    ['name' => 'Minneapolis API Hub', 'email' => 'contact@mplsapi.com', 'mobile_no' => '+9198765443273', 'website' => 'https://mplsapi.com', 'address' => '6000 Hennepin Ave, Minneapolis, MN 55408'],
                    ['name' => 'Twin Cities Integration', 'email' => 'hello@twincitiesintegration.com', 'mobile_no' => '+9198765443274', 'website' => 'https://twincitiesintegration.com', 'address' => '6100 Lyndale Ave S, Minneapolis, MN 55419'],
                    ['name' => 'Minnesota API Solutions', 'email' => 'info@mnapisolutions.com', 'mobile_no' => '+9198765443275', 'website' => 'https://mnapisolutions.com', 'address' => '6200 France Ave S, Minneapolis, MN 55435']
                ],
                // Database Consulting contacts
                [
                    ['name' => 'Kansas City Data Solutions', 'email' => 'contact@kcdata.com', 'mobile_no' => '+9198765443276', 'website' => 'https://kcdata.com', 'address' => '6300 Main St, Kansas City, MO 64111'],
                    ['name' => 'Midwest Database Experts', 'email' => 'hello@midwestdb.com', 'mobile_no' => '+9198765443277', 'website' => 'https://midwestdb.com', 'address' => '6400 Broadway Blvd, Kansas City, MO 64111'],
                    ['name' => 'Missouri Data Consulting', 'email' => 'info@modataconsulting.com', 'mobile_no' => '+9198765443278', 'website' => 'https://modataconsulting.com', 'address' => '6500 Ward Pkwy, Kansas City, MO 64113']
                ],
                // Quality Assurance Services contacts
                [
                    ['name' => 'Salt Lake QA Labs', 'email' => 'contact@slcqa.com', 'mobile_no' => '+9198765443279', 'website' => 'https://slcqa.com', 'address' => '6600 S State St, Salt Lake City, UT 84107'],
                    ['name' => 'Utah Testing Solutions', 'email' => 'hello@utahtesting.com', 'mobile_no' => '+9198765443280', 'website' => 'https://utahtesting.com', 'address' => '6700 Highland Dr, Salt Lake City, UT 84121'],
                    ['name' => 'Mountain West QA', 'email' => 'info@mountainwestqa.com', 'mobile_no' => '+9198765443281', 'website' => 'https://mountainwestqa.com', 'address' => '6800 S 700 E, Salt Lake City, UT 84047']
                ],
                // Business Intelligence contacts
                [
                    ['name' => 'Tampa BI Solutions', 'email' => 'contact@tampabi.com', 'mobile_no' => '+9198765443282', 'website' => 'https://tampabi.com', 'address' => '6900 W Kennedy Blvd, Tampa, FL 33609'],
                    ['name' => 'Florida Analytics Group', 'email' => 'hello@flanalytics.com', 'mobile_no' => '+9198765443283', 'website' => 'https://flanalytics.com', 'address' => '7000 N Dale Mabry Hwy, Tampa, FL 33614'],
                    ['name' => 'Gulf Coast Intelligence', 'email' => 'info@gulfcoastintel.com', 'mobile_no' => '+9198765443284', 'website' => 'https://gulfcoastintel.com', 'address' => '7100 W Hillsborough Ave, Tampa, FL 33634']
                ],
                // CRM Development contacts
                [
                    ['name' => 'Orlando CRM Specialists', 'email' => 'contact@orlandocrm.com', 'mobile_no' => '+9198765443285', 'website' => 'https://orlandocrm.com', 'address' => '7200 International Dr, Orlando, FL 32819'],
                    ['name' => 'Central Florida CRM', 'email' => 'hello@centralflacrm.com', 'mobile_no' => '+9198765443286', 'website' => 'https://centralflacrm.com', 'address' => '7300 Sand Lake Rd, Orlando, FL 32819'],
                    ['name' => 'Magic City Solutions', 'email' => 'info@magiccitysolutions.com', 'mobile_no' => '+9198765443287', 'website' => 'https://magiccitysolutions.com', 'address' => '7400 Colonial Dr, Orlando, FL 32818']
                ],
                // ERP Solutions contacts
                [
                    ['name' => 'Columbus ERP Systems', 'email' => 'contact@columbuserp.com', 'mobile_no' => '+9198765443288', 'website' => 'https://columbuserp.com', 'address' => '7500 High St, Columbus, OH 43215'],
                    ['name' => 'Ohio Enterprise Solutions', 'email' => 'hello@ohioenterprise.com', 'mobile_no' => '+9198765443289', 'website' => 'https://ohioenterprise.com', 'address' => '7600 Broad St, Columbus, OH 43213'],
                    ['name' => 'Buckeye Business Systems', 'email' => 'info@buckeyebusiness.com', 'mobile_no' => '+9198765443290', 'website' => 'https://buckeyebusiness.com', 'address' => '7700 Olentangy River Rd, Columbus, OH 43235']
                ],
                // Digital Transformation contacts
                [
                    ['name' => 'Indianapolis Digital Hub', 'email' => 'contact@indydigital.com', 'mobile_no' => '+9198765443291', 'website' => 'https://indydigital.com', 'address' => '7800 N Meridian St, Indianapolis, IN 46260'],
                    ['name' => 'Hoosier Tech Solutions', 'email' => 'hello@hoosiertech.com', 'mobile_no' => '+9198765443292', 'website' => 'https://hoosiertech.com', 'address' => '7900 Keystone Ave, Indianapolis, IN 46240'],
                    ['name' => 'Circle City Digital', 'email' => 'info@circlecitydigital.com', 'mobile_no' => '+9198765443293', 'website' => 'https://circlecitydigital.com', 'address' => '8000 E 96th St, Indianapolis, IN 46256']
                ],
                // Automation Services contacts
                [
                    ['name' => 'Milwaukee Automation Co', 'email' => 'contact@milwaukeeauto.com', 'mobile_no' => '+9198765443294', 'website' => 'https://milwaukeeauto.com', 'address' => '8100 W Wisconsin Ave, Milwaukee, WI 53226'],
                    ['name' => 'Wisconsin Process Solutions', 'email' => 'hello@wiprocess.com', 'mobile_no' => '+9198765443295', 'website' => 'https://wiprocess.com', 'address' => '8200 N Port Washington Rd, Milwaukee, WI 53217'],
                    ['name' => 'Great Lakes Automation', 'email' => 'info@greatlakesauto.com', 'mobile_no' => '+9198765443296', 'website' => 'https://greatlakesauto.com', 'address' => '8300 W Capitol Dr, Milwaukee, WI 53222']
                ],
                // Integration Services contacts
                [
                    ['name' => 'Richmond Integration Labs', 'email' => 'contact@richmondintegration.com', 'mobile_no' => '+9198765443297', 'website' => 'https://richmondintegration.com', 'address' => '8400 W Broad St, Richmond, VA 23294'],
                    ['name' => 'Virginia Tech Solutions', 'email' => 'hello@vatechsolutions.com', 'mobile_no' => '+9198765443298', 'website' => 'https://vatechsolutions.com', 'address' => '8500 Forest Hill Ave, Richmond, VA 23225'],
                    ['name' => 'Capital City Systems', 'email' => 'info@capitalcitysystems.com', 'mobile_no' => '+9198765443299', 'website' => 'https://capitalcitysystems.com', 'address' => '8600 Patterson Ave, Richmond, VA 23229']
                ],
                // Tech Startups contacts
                [
                    ['name' => 'Austin Startup Incubator', 'email' => 'contact@austinstartup.com', 'mobile_no' => '+9198765443300', 'website' => 'https://austinstartup.com', 'address' => '8700 Research Blvd, Austin, TX 78758'],
                    ['name' => 'Texas Innovation Hub', 'email' => 'hello@texasinnovation.com', 'mobile_no' => '+9198765443301', 'website' => 'https://texasinnovation.com', 'address' => '8800 Burnet Rd, Austin, TX 78757'],
                    ['name' => 'Capital City Ventures', 'email' => 'info@capitalcityventures.com', 'mobile_no' => '+9198765443302', 'website' => 'https://capitalcityventures.com', 'address' => '8900 N Lamar Blvd, Austin, TX 78753']
                ]
            ];

            foreach ($leadSearchData as $index => $leadData) {
                $hour = rand(0, 23);
                $minute = rand(0, 59);
                $second = rand(0, 59);

                // Create the founded lead
                $foundedLead = FindGoogleLeadFoundedLead::create([
                    'name' => $leadData['name'],
                    'keywords' => $leadData['keywords'],
                    'address' => $leadData['address'],
                    'contact' => 0, // Will be updated after contacts are created
                    'creator_id' => $userId,
                    'created_by' => $userId,
                    'created_at' => $leadData['created_at']->setTime($hour, $minute, $second)->format('Y-m-d H:i:s'),
                ]);

                // Create contacts for this lead
                $leadContacts = $contactsData[$index] ?? [];
                $contactCount = 0;

                foreach ($leadContacts as $contactData) {
                    $contactHour = rand(0, 23);
                    $contactMinute = rand(0, 59);
                    $contactSecond = rand(0, 59);

                    FindGoogleLeadFoundedLeadContact::create([
                        'founded_lead_id' => $foundedLead->id,
                        'is_lead' => rand(0, 1) ? '1' : '0',
                        'is_sync' => rand(0, 1) ? '1' : '0',
                        'name' => $contactData['name'],
                        'email' => $contactData['email'],
                        'mobile_no' => $contactData['mobile_no'],
                        'website' => $contactData['website'],
                        'address' => $contactData['address'],
                        'creator_id' => $userId,
                        'created_by' => $userId,
                        'created_at' => $leadData['created_at']->addMinutes(rand(5, 30))->setTime($contactHour, $contactMinute, $contactSecond)->format('Y-m-d H:i:s'),
                    ]);

                    $contactCount++;
                }

                // Update the contact count for the founded lead
                $foundedLead->update(['contact' => $contactCount]);
            }

            Model::reguard();
        }
    }
}

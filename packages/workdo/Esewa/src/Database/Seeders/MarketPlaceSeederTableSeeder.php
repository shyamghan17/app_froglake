<?php

namespace Workdo\Esewa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\LandingPage\Entities\MarketplacePageSetting;


class MarketPlaceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module = 'Esewa';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Esewa';
        $data['product_main_description'] = '<p>The eSewa Payment Gateway Integration in Dash SAAS provides a robust and secure solution for handling online transactions. eSewa, a widely trusted digital wallet and payment gateway in Nepal, allows businesses to offer a seamless payment experience to their customers. By integrating eSewa with Dash SAAS, you can expand your payment options, enhance security, and improve the overall efficiency of your financial transactions.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Esewa Payment Gateway';
        $data['dedicated_theme_description'] = '<p>eSewa, a widely trusted digital wallet and payment gateway in Nepal, allows businesses to offer a seamless payment experience to their customers.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"",
                                                "dedicated_theme_section_heading":"Why use Esewa payment?","dedicated_theme_section_description":"<p>Integrating eSewa into your Dash SAAS platform begins with a straightforward setup process. First, you need to obtain your eSewa merchant credentials, which include your Merchant ID. These credentials can be obtained by registering your business with eSewa and following their verification process.<\/p>",
                                                "dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},

                                        {"dedicated_theme_section_image":"",
                                        "dedicated_theme_section_heading":"Esewa payment gateway",
                                        "dedicated_theme_section_description":"<p>Security is a critical aspect of online transactions, and the eSewa Payment Gateway Integration in Dash SAAS is designed to ensure the highest level of protection for both businesses and customers. eSewa utilizes advanced encryption technologies to safeguard transaction data and prevent fraud.<\/p>",
                                        "dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Esewa"},{"screenshots":"","screenshots_heading":"Esewa"},{"screenshots":"","screenshots_heading":"Esewa"},{"screenshots":"","screenshots_heading":"Esewa"},{"screenshots":"","screenshots_heading":"Esewa"}]';
        $data['addon_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach ($data as $key => $value) {
            if (!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()) {
                MarketplacePageSetting::updateOrCreate(
                    [
                        'name' => $key,
                        'module' => $module

                    ],
                    [
                        'value' => $value
                    ]
                );
            }
        }
    }
}

<?php

namespace Workdo\Khalti\Database\Seeders;

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
        $module = 'Khalti';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Khalti';
        $data['product_main_description'] = '<p>Khalti simplifies transactions for users of varying technical expertise. Khalti employs robust security protocols and encryption techniques, ensuring the safety of users financial data during transactions. Khalti payment offers a comprehensive solution with convenience, security, accessibility, and financial management features, making it an attractive choice for users looking for a versatile and secure platform for their everyday transactions.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Khalti <b>Payment</b> Gateway</h2>';
        $data['dedicated_theme_description'] = '<p>Khalti supports various payment methods like Khalti balance, bank account linking, mobile banking, debit/credit cards, and connectIPS, allowing users to choose their preferred payment mode.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"API Key Settings Page for Khalti Integration","dedicated_theme_section_description":"<p>Configuring Khalti integration has never been easier. With our intuitive API Key settings page, you can seamlessly set up and manage your Khalti integration to ensure a smooth and secure payment process.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Invoices & Plan Payments using Khalti Payment","dedicated_theme_section_description":"<p>Streamline your payment process with electronic invoices (retainer and sales) generation,  plan payment, secure delivery, and easy payment initiation and authorization through the Khalti Payment platform.
            <\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Khalti"},{"screenshots":"","screenshots_heading":"Khalti"},{"screenshots":"","screenshots_heading":"Khalti"},{"screenshots":"","screenshots_heading":"Khalti"},{"screenshots":"","screenshots_heading":"Khalti"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules <b>for your business?</b></h2>';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Empower Your Workforce with DASH';
        $data['whychoose_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_heading'] = '<h2>Why choose dedicated modules <b>for your business?</b></h2>';
        $data['pricing_plan_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach($data as $key => $value){
            if(!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()){
                MarketplacePageSetting::updateOrCreate(
                [
                    'name' => $key,
                    'module' => $module

                ],
                [
                    'value' => $value
                ]);
            }
        }
    }
}

<?php

namespace Tests\Feature\Lead;

use App\Models\User;
use App\Models\AddOn;
use App\Models\UserActiveModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Workdo\Lead\Models\Lead;
use Workdo\Lead\Models\LeadStage;
use Workdo\Lead\Models\Pipeline;

class LeadWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_create_lead_with_website(): void
    {
        $company = User::factory()->create([
            'type' => 'company',
        ]);

        if (!$company instanceof User) {
            $this->fail('Company user was not created.');
        }

        AddOn::create([
            'module' => 'Lead',
            'name' => 'Lead',
            'monthly_price' => 0,
            'yearly_price' => 0,
            'package_name' => 'Lead',
            'priority' => 1,
            'is_enable' => 1,
            'for_admin' => 0,
        ]);

        UserActiveModule::create([
            'user_id' => $company->id,
            'module' => 'Lead',
        ]);

        Permission::firstOrCreate(
            ['name' => 'create-leads', 'guard_name' => 'web'],
            ['add_on' => 'Lead', 'module' => 'Lead', 'label' => 'Create Leads']
        );
        $company->givePermissionTo('create-leads');

        $pipeline = Pipeline::create([
            'name' => 'Default',
            'creator_id' => $company->id,
            'created_by' => $company->id,
        ]);

        LeadStage::create([
            'name' => 'New',
            'order' => 0,
            'pipeline_id' => $pipeline->id,
            'creator_id' => $company->id,
            'created_by' => $company->id,
        ]);

        $this->actingAs($company)
            ->post(route('lead.leads.store'), [
                'name' => 'Test Lead',
                'company_name' => 'Acme Pvt Ltd',
                'email' => 'lead@example.com',
                'subject' => 'Hello',
                'user_id' => $company->id,
                'phone' => '+15551234567',
                'date' => now()->toDateString(),
                'website' => 'https://example.com',
                'category' => 'B2B',
                'address' => 'Somewhere',
                'district' => 'Kathmandu',
                'province' => 'Bagmati',
                'remarks' => 'Test remarks',
                'is_live' => true,
                'company_pan' => '123456789',
                'lead_status' => 'New',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('leads', [
            'email' => 'lead@example.com',
            'created_by' => $company->id,
        ]);

        $lead = Lead::query()->where('email', 'lead@example.com')->first();

        $this->assertNotNull($lead);
        $this->assertSame('Acme Pvt Ltd', $lead->company_name);
        $this->assertSame('https://example.com', $lead->website);
        $this->assertSame('B2B', $lead->category);
        $this->assertSame('Somewhere', $lead->address);
        $this->assertSame('Kathmandu', $lead->district);
        $this->assertSame('Bagmati', $lead->province);
        $this->assertSame('Test remarks', $lead->remarks);
        $this->assertTrue((bool) $lead->is_live);
        $this->assertSame('123456789', $lead->company_pan);
        $this->assertSame('New', $lead->lead_status);
    }
}

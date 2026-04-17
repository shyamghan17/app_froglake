<?php

namespace Workdo\ActivityLog\Providers;

use App\Events\CreateUser;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\ActivityLog\Listeners\AppointmentStatusLis;
use Workdo\ActivityLog\Listeners\CreateAppointmentLis;
use Workdo\ActivityLog\Listeners\CreateChallengeLis;
use Workdo\ActivityLog\Listeners\CreateCleaningBookingLis;
use Workdo\ActivityLog\Listeners\CreateCleaningTeamLis;
use Workdo\ActivityLog\Listeners\CreateCmmsposLis;
use Workdo\ActivityLog\Listeners\CreateComponentLis;
use Workdo\ActivityLog\Listeners\CreateContractLis;
use Workdo\ActivityLog\Listeners\CreateCreativityLis;
use Workdo\ActivityLog\Listeners\CreateDealLis;
use Workdo\ActivityLog\Listeners\CreateDocumentLis;
use Workdo\ActivityLog\Listeners\CreateLocationLis;
use Workdo\ActivityLog\Listeners\CreatePreventiveMaintenanceLis;
use Workdo\ActivityLog\Listeners\CreateQuestionLis;
use Workdo\ActivityLog\Listeners\CreateScheduleLis;
use Workdo\ActivityLog\Listeners\CreateSupplierLis;
use Workdo\ActivityLog\Listeners\CreateDealTaskLis;
use Workdo\ActivityLog\Listeners\CreateFileLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentAssetLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentAuditLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentComponentLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentConsumableLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentLicenseLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentMaintenanceLis;
use Workdo\ActivityLog\Listeners\CreateFixEquipmentPreDefinedKitLis;
use Workdo\ActivityLog\Listeners\CreateFreightBookingRequestLis;
use Workdo\ActivityLog\Listeners\CreateFreightCustomerLis;
use Workdo\ActivityLog\Listeners\CreateHospitalAppointmentLis;
use Workdo\ActivityLog\Listeners\CreateHospitalBedLis;
use Workdo\ActivityLog\Listeners\CreateHospitalDoctorLis;
use Workdo\ActivityLog\Listeners\CreateHospitalLabTestLis;
use Workdo\ActivityLog\Listeners\CreateHospitalMedicineLis;
use Workdo\ActivityLog\Listeners\CreateHospitalPatientLis;
use Workdo\ActivityLog\Listeners\CreateInsuranceLis;
use Workdo\ActivityLog\Listeners\CreateInternalknowledgeArticleLis;
use Workdo\ActivityLog\Listeners\CreateInternalknowledgeBookLis;
use Workdo\ActivityLog\Listeners\CreateLeadLis;
use Workdo\ActivityLog\Listeners\CreateMachineInsuranceLis;
use Workdo\ActivityLog\Listeners\CreateMachineLis;
use Workdo\ActivityLog\Listeners\CreateMachineRepairRequestLis;
use Workdo\ActivityLog\Listeners\CreateNoteLis;
use Workdo\ActivityLog\Listeners\CreatePolicyLis;
use Workdo\ActivityLog\Listeners\CreatePosLis;
use Workdo\ActivityLog\Listeners\CreateProjectLis;
use Workdo\ActivityLog\Listeners\CreateProjectMilestoneLis;
use Workdo\ActivityLog\Listeners\CreateProjectTaskLis;
use Workdo\ActivityLog\Listeners\CreateRepairOrderRequestLis;
use Workdo\ActivityLog\Listeners\CreateRepairProductPartLis;
use Workdo\ActivityLog\Listeners\CreateSalesAccountLis;
use Workdo\ActivityLog\Listeners\CreateSalesCallLis;
use Workdo\ActivityLog\Listeners\CreateSalesContactLis;
use Workdo\ActivityLog\Listeners\CreateSalesDocumentLis;
use Workdo\ActivityLog\Listeners\CreateSalesInvoiceLis;
use Workdo\ActivityLog\Listeners\CreateSalesMeetingLis;
use Workdo\ActivityLog\Listeners\CreateSalesOpportunityLis;
use Workdo\ActivityLog\Listeners\CreateSalesOrderLis;
use Workdo\ActivityLog\Listeners\CreateSalesQuoteLis;
use Workdo\ActivityLog\Listeners\CreateServiceLis;
use Workdo\ActivityLog\Listeners\CreateTemplateLis;
use Workdo\ActivityLog\Listeners\CreateUserLis;
use Workdo\ActivityLog\Listeners\CreateVehicleLis;
use Workdo\ActivityLog\Listeners\CreateVerificationLis;
use Workdo\ActivityLog\Listeners\CreateVisitorLis;
use Workdo\ActivityLog\Listeners\CreateVisitorLogLis;
use Workdo\ActivityLog\Listeners\DealAddCallLis;
use Workdo\ActivityLog\Listeners\DealAddClientLis;
use Workdo\ActivityLog\Listeners\DealAddDiscussionLis;
use Workdo\ActivityLog\Listeners\DealAddEmailLis;
use Workdo\ActivityLog\Listeners\DealAddProductLis;
use Workdo\ActivityLog\Listeners\DealAddUserLis;
use Workdo\ActivityLog\Listeners\DealCallUpdateLis;
use Workdo\ActivityLog\Listeners\DealMovedLis;
use Workdo\ActivityLog\Listeners\DealSourceUpdateLis;
use Workdo\ActivityLog\Listeners\DealUploadFileLis;
use Workdo\ActivityLog\Listeners\LeadAddCallLis;
use Workdo\ActivityLog\Listeners\LeadAddDiscussionLis;
use Workdo\ActivityLog\Listeners\LeadAddEmailLis;
use Workdo\ActivityLog\Listeners\LeadAddProductLis;
use Workdo\ActivityLog\Listeners\LeadAddUserLis;
use Workdo\ActivityLog\Listeners\LeadConvertDealLis;
use Workdo\ActivityLog\Listeners\LeadMovedLis;
use Workdo\ActivityLog\Listeners\LeadSourceUpdateLis;
use Workdo\ActivityLog\Listeners\LeadUpdateCallLis;
use Workdo\ActivityLog\Listeners\LeadUploadFileLis;
use Workdo\ActivityLog\Listeners\ProjectShareToClientLis;
use Workdo\ActivityLog\Listeners\StatusChangeDealTaskLis;
use Workdo\ActivityLog\Listeners\StatusChangeContractLis;
use Workdo\ActivityLog\Listeners\UpdateAppointmentLis;
use Workdo\ActivityLog\Listeners\UpdateChallengeLis;
use Workdo\ActivityLog\Listeners\UpdateDealLis;
use Workdo\ActivityLog\Listeners\UpdateCleaningBookingLis;
use Workdo\ActivityLog\Listeners\UpdateCleaningTeamLis;
use Workdo\ActivityLog\Listeners\UpdateCmmsPosLis;
use Workdo\ActivityLog\Listeners\UpdateComponentLis;
use Workdo\ActivityLog\Listeners\UpdateContractLis;
use Workdo\ActivityLog\Listeners\UpdateCreativityLis;
use Workdo\ActivityLog\Listeners\UpdateDealTaskLis;
use Workdo\ActivityLog\Listeners\UpdateDocumentLis;
use Workdo\ActivityLog\Listeners\UpdateFileLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentAssetLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentAuditLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentComponentLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentConsumableLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentLicenseLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentMaintenanceLis;
use Workdo\ActivityLog\Listeners\UpdateFixEquipmentPreDefinedKitLis;
use Workdo\ActivityLog\Listeners\UpdateFreightBookingRequestLis;
use Workdo\ActivityLog\Listeners\UpdateFreightCustomerLis;
use Workdo\ActivityLog\Listeners\UpdateHospitalAppointmentLis;
use Workdo\ActivityLog\Listeners\UpdateHospitalBedLis;
use Workdo\ActivityLog\Listeners\UpdateHospitalDoctorLis;
use Workdo\ActivityLog\Listeners\UpdateHospitalLabTestLis;
use Workdo\ActivityLog\Listeners\UpdateHospitalMedicineLis;
use Workdo\ActivityLog\Listeners\UpdateInsuranceLis;
use Workdo\ActivityLog\Listeners\UpdateInternalknowledgeArticleLis;
use Workdo\ActivityLog\Listeners\UpdateInternalknowledgeBookLis;
use Workdo\ActivityLog\Listeners\UpdateLeadLis;
use Workdo\ActivityLog\Listeners\UpdateLocationLis;
use Workdo\ActivityLog\Listeners\UpdateMachineInsuranceLis;
use Workdo\ActivityLog\Listeners\UpdateMachineLis;
use Workdo\ActivityLog\Listeners\UpdateMachineRepairRequestLis;
use Workdo\ActivityLog\Listeners\UpdateNoteLis;
use Workdo\ActivityLog\Listeners\UpdatePolicyLis;
use Workdo\ActivityLog\Listeners\UpdatePreventiveMaintenanceLis;
use Workdo\ActivityLog\Listeners\UpdateProjectLis;
use Workdo\ActivityLog\Listeners\UpdateProjectMilestoneLis;
use Workdo\ActivityLog\Listeners\UpdateProjectTaskLis;
use Workdo\ActivityLog\Listeners\UpdateQuestionLis;
use Workdo\ActivityLog\Listeners\UpdateRepairOrderRequestLis;
use Workdo\ActivityLog\Listeners\UpdateRepairProductPartLis;
use Workdo\ActivityLog\Listeners\UpdateSalesAccountLis;
use Workdo\ActivityLog\Listeners\UpdateSalesCallLis;
use Workdo\ActivityLog\Listeners\UpdateSalesContactLis;
use Workdo\ActivityLog\Listeners\UpdateSalesDocumentLis;
use Workdo\ActivityLog\Listeners\UpdateSalesInvoiceLis;
use Workdo\ActivityLog\Listeners\UpdateSalesMeetingLis;
use Workdo\ActivityLog\Listeners\UpdateSalesOpportunityLis;
use Workdo\ActivityLog\Listeners\UpdateSalesOrderLis;
use Workdo\ActivityLog\Listeners\UpdateSalesQuoteLis;
use Workdo\ActivityLog\Listeners\UpdateServiceLis;
use Workdo\ActivityLog\Listeners\UpdateSupplierLis;
use Workdo\ActivityLog\Listeners\UpdateTemplateLis;
use Workdo\ActivityLog\Listeners\UpdateVehicleLis;
use Workdo\ActivityLog\Listeners\UpdateVerificationLis;
use Workdo\ActivityLog\Listeners\UpdateVisitorLis;
use Workdo\ActivityLog\Listeners\UpdateVisitorLogLis;
use Workdo\Appointment\Events\AppointmentStatus;
use Workdo\Appointment\Events\CreateAppointment;
use Workdo\Appointment\Events\CreateQuestion;
use Workdo\Appointment\Events\CreateSchedule;
use Workdo\Appointment\Events\UpdateAppointment;
use Workdo\Appointment\Events\UpdateQuestion;
use Workdo\CleaningManagement\Events\CreateCleaningBooking;
use Workdo\CleaningManagement\Events\CreateCleaningTeam;
use Workdo\CleaningManagement\Events\UpdateCleaningBooking;
use Workdo\CleaningManagement\Events\UpdateCleaningTeam;
use Workdo\CMMS\Events\CreateCmmsPos;
use Workdo\CMMS\Events\CreateComponent;
use Workdo\CMMS\Events\CreateLocation;
use Workdo\CMMS\Events\CreatePreventiveMaintenance;
use Workdo\CMMS\Events\CreateSupplier;
use Workdo\CMMS\Events\UpdateCmmsPos;
use Workdo\CMMS\Events\UpdateComponent;
use Workdo\CMMS\Events\UpdateLocation;
use Workdo\CMMS\Events\UpdatePreventiveMaintenance;
use Workdo\CMMS\Events\UpdateSupplier;
use Workdo\Contract\Events\CreateContract;
use Workdo\Contract\Events\StatusChangeContract;
use Workdo\Contract\Events\UpdateContract;
use Workdo\Documents\Events\CreateDocument;
use Workdo\Documents\Events\UpdateDocument;
use Workdo\Feedback\Events\CreateTemplate;
use Workdo\Feedback\Events\UpdateTemplate;
use Workdo\FileSharing\Events\CreateFile;
use Workdo\FileSharing\Events\CreateVerification;
use Workdo\FileSharing\Events\UpdateFile;
use Workdo\FileSharing\Events\UpdateVerification;
use Workdo\FixEquipment\Events\CreateFixEquipmentAsset;
use Workdo\FixEquipment\Events\CreateFixEquipmentAudit;
use Workdo\FixEquipment\Events\CreateFixEquipmentComponent;
use Workdo\FixEquipment\Events\CreateFixEquipmentConsumable;
use Workdo\FixEquipment\Events\CreateFixEquipmentLicense;
use Workdo\FixEquipment\Events\CreateFixEquipmentMaintenance;
use Workdo\FixEquipment\Events\CreateFixEquipmentPreDefinedKit;
use Workdo\FixEquipment\Events\UpdateFixEquipmentAsset;
use Workdo\FixEquipment\Events\UpdateFixEquipmentAudit;
use Workdo\FixEquipment\Events\UpdateFixEquipmentComponent;
use Workdo\FixEquipment\Events\UpdateFixEquipmentConsumable;
use Workdo\FixEquipment\Events\UpdateFixEquipmentLicense;
use Workdo\FixEquipment\Events\UpdateFixEquipmentMaintenance;
use Workdo\FixEquipment\Events\UpdateFixEquipmentPreDefinedKit;
use Workdo\FreightManagementSystem\Events\CreateFreightBookingRequest;
use Workdo\FreightManagementSystem\Events\CreateFreightCustomer;
use Workdo\FreightManagementSystem\Events\UpdateFreightBookingRequest;
use Workdo\FreightManagementSystem\Events\UpdateFreightCustomer;
use Workdo\GarageManagement\Events\CreateService;
use Workdo\GarageManagement\Events\CreateVehicle;
use Workdo\GarageManagement\Events\UpdateService;
use Workdo\GarageManagement\Events\UpdateVehicle;
use Workdo\HospitalManagement\Events\CreateHospitalAppointment;
use Workdo\HospitalManagement\Events\CreateHospitalBed;
use Workdo\HospitalManagement\Events\CreateHospitalDoctor;
use Workdo\HospitalManagement\Events\CreateHospitalLabTest;
use Workdo\HospitalManagement\Events\CreateHospitalMedicine;
use Workdo\HospitalManagement\Events\CreateHospitalPatient;
use Workdo\HospitalManagement\Events\UpdateHospitalAppointment;
use Workdo\HospitalManagement\Events\UpdateHospitalBed;
use Workdo\HospitalManagement\Events\UpdateHospitalDoctor;
use Workdo\HospitalManagement\Events\UpdateHospitalLabTest;
use Workdo\HospitalManagement\Events\UpdateHospitalMedicine;
use Workdo\InnovationCenter\Events\CreateChallenge;
use Workdo\InnovationCenter\Events\CreateCreativity;
use Workdo\InnovationCenter\Events\UpdateChallenge;
use Workdo\InnovationCenter\Events\UpdateCreativity;
use Workdo\InsuranceManagement\Events\CreateInsurance;
use Workdo\InsuranceManagement\Events\CreatePolicy;
use Workdo\InsuranceManagement\Events\UpdateInsurance;
use Workdo\InsuranceManagement\Events\UpdatePolicy;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeArticle;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeBook;
use Workdo\Internalknowledge\Events\UpdateInternalknowledgeArticle;
use Workdo\Internalknowledge\Events\UpdateInternalknowledgeBook;
use Workdo\Lead\Events\CreateDeal;
use Workdo\Lead\Events\CreateDealTask;
use Workdo\Lead\Events\CreateLead;
use Workdo\Lead\Events\DealAddCall;
use Workdo\Lead\Events\DealAddClient;
use Workdo\Lead\Events\DealAddDiscussion;
use Workdo\Lead\Events\DealAddEmail;
use Workdo\Lead\Events\DealAddProduct;
use Workdo\Lead\Events\DealAddUser;
use Workdo\Lead\Events\DealCallUpdate;
use Workdo\Lead\Events\DealMoved;
use Workdo\Lead\Events\DealSourceUpdate;
use Workdo\Lead\Events\DealUploadFile;
use Workdo\Lead\Events\LeadAddCall;
use Workdo\Lead\Events\LeadAddDiscussion;
use Workdo\Lead\Events\LeadAddEmail;
use Workdo\Lead\Events\LeadAddProduct;
use Workdo\Lead\Events\LeadAddUser;
use Workdo\Lead\Events\LeadCallUpdate;
use Workdo\Lead\Events\LeadConvertDeal;
use Workdo\Lead\Events\LeadMoved;
use Workdo\Lead\Events\LeadSourceUpdate;
use Workdo\Lead\Events\LeadUploadFile;
use Workdo\Lead\Events\StatusChangeDealTask;
use Workdo\Lead\Events\UpdateDeal;
use Workdo\Lead\Events\UpdateDealTask;
use Workdo\Lead\Events\UpdateLead;
use Workdo\MachineRepairManagement\Events\CreateMachine;
use Workdo\MachineRepairManagement\Events\CreateMachineInsurance;
use Workdo\MachineRepairManagement\Events\CreateMachineRepairRequest;
use Workdo\MachineRepairManagement\Events\UpdateMachine;
use Workdo\MachineRepairManagement\Events\UpdateMachineInsurance;
use Workdo\MachineRepairManagement\Events\UpdateMachineRepairRequest;
use Workdo\Notes\Events\CreateNote;
use Workdo\Notes\Events\UpdateNote;
use Workdo\Pos\Events\CreatePos;
use Workdo\RepairManagementSystem\Events\CreateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\CreateRepairProductPart;
use Workdo\RepairManagementSystem\Events\UpdateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\UpdateRepairProductPart;
use Workdo\Sales\Events\CreateSalesAccount;
use Workdo\Sales\Events\CreateSalesCall;
use Workdo\Sales\Events\CreateSalesContact;
use Workdo\Sales\Events\CreateSalesDocument;
use Workdo\Sales\Events\CreateSalesInvoice;
use Workdo\Sales\Events\CreateSalesMeeting;
use Workdo\Sales\Events\CreateSalesOpportunity;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\Sales\Events\CreateSalesQuote;
use Workdo\Sales\Events\UpdateSalesAccount;
use Workdo\Sales\Events\UpdateSalesCall;
use Workdo\Sales\Events\UpdateSalesContact;
use Workdo\Sales\Events\UpdateSalesDocument;
use Workdo\Sales\Events\UpdateSalesInvoice;
use Workdo\Sales\Events\UpdateSalesMeeting;
use Workdo\Sales\Events\UpdateSalesOpportunity;
use Workdo\Sales\Events\UpdateSalesOrder;
use Workdo\Sales\Events\UpdateSalesQuote;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateProjectMilestone;
use Workdo\Taskly\Events\CreateProjectTask;
use Workdo\Taskly\Events\ProjectShareToClient;
use Workdo\Taskly\Events\UpdateProject;
use Workdo\Taskly\Events\UpdateProjectMilestone;
use Workdo\Taskly\Events\UpdateProjectTask;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\VisitorManagement\Events\CreateVisitorLog;
use Workdo\VisitorManagement\Events\UpdateVisitor;
use Workdo\VisitorManagement\Events\UpdateVisitorLog;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreateUser::class => [
            CreateUserLis::class
        ],
        CreateLead::class => [
            CreateLeadLis::class
        ],
        UpdateLead::class => [
            UpdateLeadLis::class
        ],
        CreateDeal::class => [
            CreateDealLis::class
        ],
        CreateDealTask::class => [
            CreateDealTaskLis::class
        ],
        DealAddCall::class => [
            DealAddCallLis::class
        ],
        DealAddClient::class => [
            DealAddClientLis::class
        ],
        DealAddDiscussion::class => [
            DealAddDiscussionLis::class
        ],
        DealAddEmail::class => [
            DealAddEmailLis::class
        ],       
        DealAddProduct::class => [
            DealAddProductLis::class
        ],
        DealAddUser::class => [
            DealAddUserLis::class
        ],
        DealCallUpdate::class => [
            DealCallUpdateLis::class
        ],
        DealMoved::class => [
            DealMovedLis::class
        ],
        DealSourceUpdate::class => [
            DealSourceUpdateLis::class
        ],
        DealUploadFile::class => [
            DealUploadFileLis::class
        ],
        LeadAddCall::class => [
            LeadAddCallLis::class
        ],
        LeadAddDiscussion::class => [
            LeadAddDiscussionLis::class
        ],
        LeadAddEmail::class => [
            LeadAddEmailLis::class
        ],       
        LeadAddProduct::class => [
            LeadAddProductLis::class
        ],
        LeadAddUser::class => [
            LeadAddUserLis::class
        ],
        LeadConvertDeal::class => [
            LeadConvertDealLis::class
        ],
        LeadMoved::class => [
            LeadMovedLis::class
        ],
        LeadSourceUpdate::class => [
            LeadSourceUpdateLis::class
        ],
        LeadCallUpdate::class => [
            LeadUpdateCallLis::class
        ],
        LeadUploadFile::class => [
            LeadUploadFileLis::class
        ],
        StatusChangeDealTask::class => [
            StatusChangeDealTaskLis::class
        ],
        UpdateDeal::class => [
            UpdateDealLis::class
        ],
        UpdateDealTask::class => [
            UpdateDealTaskLis::class
        ],
        CreateAppointment::class => [
            CreateAppointmentLis::class
        ],
        CreateSchedule::class => [
            CreateScheduleLis::class
        ],
        AppointmentStatus::class => [
            AppointmentStatusLis::class
        ],
        CreateQuestion::class => [
            CreateQuestionLis::class
        ],
        UpdateAppointment::class => [
            UpdateAppointmentLis::class
        ],
        UpdateQuestion::class => [
            UpdateQuestionLis::class
        ],       
        CreateCleaningBooking::class => [
            CreateCleaningBookingLis::class
        ],
        CreateCleaningTeam::class => [
            CreateCleaningTeamLis::class
        ],
        UpdateCleaningBooking::class => [
            UpdateCleaningBookingLis::class
        ],
        UpdateCleaningTeam::class => [
            UpdateCleaningTeamLis::class
        ],
        CreateContract::class => [
            CreateContractLis::class
        ],
        StatusChangeContract::class => [
            StatusChangeContractLis::class
        ],
        UpdateContract::class => [
            UpdateContractLis::class
        ],
        CreateCmmsPos::class => [
            CreateCmmsposLis::class
        ],
        CreateComponent::class => [
            CreateComponentLis::class
        ],
        CreateLocation::class => [
            CreateLocationLis::class
        ],
        CreatePreventiveMaintenance::class => [
            CreatePreventiveMaintenanceLis::class
        ],
        CreateSupplier::class => [
            CreateSupplierLis::class
        ],
        UpdateCmmsPos::class => [
            UpdateCmmsPosLis::class
        ],
        UpdateComponent::class => [
            UpdateComponentLis::class
        ],
        UpdateLocation::class => [
            UpdateLocationLis::class
        ],
        UpdatePreventiveMaintenance::class => [
            UpdatePreventiveMaintenanceLis::class
        ],
        UpdateSupplier::class => [
            UpdateSupplierLis::class
        ],
        CreateDocument::class => [
            CreateDocumentLis::class
        ],
        UpdateDocument::class => [
            UpdateDocumentLis::class
        ],
        CreateTemplate::class => [
            CreateTemplateLis::class
        ],
        UpdateTemplate::class => [
            UpdateTemplateLis::class
        ],
        CreateFile::class => [
            CreateFileLis::class
        ],
        UpdateFile::class => [
            UpdateFileLis::class
        ],
        CreateVerification::class => [
            CreateVerificationLis::class
        ],
        UpdateVerification::class => [
            UpdateVerificationLis::class
        ],
        CreateFixEquipmentAsset::class => [
            CreateFixEquipmentAssetLis::class
        ],
        CreateFixEquipmentAudit::class => [
            CreateFixEquipmentAuditLis::class
        ],
        CreateFixEquipmentComponent::class => [
            CreateFixEquipmentComponentLis::class
        ],
        CreateFixEquipmentConsumable::class => [
            CreateFixEquipmentConsumableLis::class
        ],
        CreateFixEquipmentLicense::class => [
            CreateFixEquipmentLicenseLis::class
        ],
        CreateFixEquipmentMaintenance::class => [
            CreateFixEquipmentMaintenanceLis::class
        ], 
        CreateFixEquipmentPreDefinedKit::class => [
            CreateFixEquipmentPreDefinedKitLis::class
        ],
        UpdateFixEquipmentAsset::class => [
            UpdateFixEquipmentAssetLis::class
        ],
        UpdateFixEquipmentAudit::class => [
            UpdateFixEquipmentAuditLis::class
        ],
        UpdateFixEquipmentComponent::class => [
            UpdateFixEquipmentComponentLis::class
        ],
        UpdateFixEquipmentConsumable::class => [
            UpdateFixEquipmentConsumableLis::class
        ],
        UpdateFixEquipmentLicense::class => [
            UpdateFixEquipmentLicenseLis::class
        ],
        UpdateFixEquipmentMaintenance::class => [
            UpdateFixEquipmentMaintenanceLis::class
        ],
        UpdateFixEquipmentPreDefinedKit::class => [
            UpdateFixEquipmentPreDefinedKitLis::class
        ],
        CreateFreightBookingRequest::class => [
            CreateFreightBookingRequestLis::class
        ],
        CreateFreightCustomer::class => [
            CreateFreightCustomerLis::class
        ],
        UpdateFreightBookingRequest::class => [
            UpdateFreightBookingRequestLis::class
        ], 
        UpdateFreightCustomer::class => [
            UpdateFreightCustomerLis::class
        ],
        CreateService::class => [
            CreateServiceLis::class
        ],
        CreateVehicle::class => [
            CreateVehicleLis::class
        ],
        UpdateService::class => [
            UpdateServiceLis::class
        ],
        UpdateVehicle::class => [
            UpdateVehicleLis::class
        ],
        CreateHospitalAppointment::class => [
            CreateHospitalAppointmentLis::class
        ],
        CreateHospitalBed::class => [
            CreateHospitalBedLis::class
        ],
        CreateHospitalDoctor::class => [
            CreateHospitalDoctorLis::class
        ],
        CreateHospitalLabTest::class => [
            CreateHospitalLabTestLis::class
        ],
        CreateHospitalMedicine::class => [
            CreateHospitalMedicineLis::class
        ],
        CreateHospitalPatient::class => [
            CreateHospitalPatientLis::class
        ],
        UpdateHospitalAppointment::class => [
            UpdateHospitalAppointmentLis::class
        ],
        UpdateHospitalBed::class => [
            UpdateHospitalBedLis::class
        ],
        UpdateHospitalDoctor::class => [
            UpdateHospitalDoctorLis::class
        ],
        UpdateHospitalLabTest::class => [
            UpdateHospitalLabTestLis::class
        ],
        UpdateHospitalMedicine::class => [
            UpdateHospitalMedicineLis::class
        ], 
        CreateChallenge::class => [
            CreateChallengeLis::class
        ],
        CreateCreativity::class => [
            CreateCreativityLis::class
        ], 
        UpdateChallenge::class => [
            UpdateChallengeLis::class
        ],
        UpdateCreativity::class => [
            UpdateCreativityLis::class
        ],
        CreateInsurance::class => [
            CreateInsuranceLis::class
        ], 
        CreatePolicy::class => [
            CreatePolicyLis::class
        ],
        UpdateInsurance::class => [
            UpdateInsuranceLis::class
        ],
        UpdatePolicy::class => [
            UpdatePolicyLis::class
        ],
        CreateInternalknowledgeArticle::class => [
            CreateInternalknowledgeArticleLis::class
        ],
        CreateInternalknowledgeBook::class => [
            CreateInternalknowledgeBookLis::class
        ],
        UpdateInternalknowledgeArticle::class => [
            UpdateInternalknowledgeArticleLis::class
        ],
        UpdateInternalknowledgeBook::class => [
            UpdateInternalknowledgeBookLis::class
        ],
        CreateMachine::class => [
            CreateMachineLis::class
        ],
        CreateMachineRepairRequest::class => [
            CreateMachineRepairRequestLis::class
        ],
        CreateMachineInsurance::class => [
            CreateMachineInsuranceLis::class
        ], 
        UpdateMachine::class => [
            UpdateMachineLis::class
        ],
        UpdateMachineRepairRequest::class => [
            UpdateMachineRepairRequestLis::class
        ],
        UpdateMachineInsurance::class => [
            UpdateMachineInsuranceLis::class
        ],
        CreateNote::class => [
            CreateNoteLis::class
        ],
        UpdateNote::class => [
            UpdateNoteLis::class
        ],
        CreatePos::class => [
            CreatePosLis::class
        ],
        CreateRepairOrderRequest::class => [
            CreateRepairOrderRequestLis::class
        ],
        CreateRepairProductPart::class => [
            CreateRepairProductPartLis::class
        ],
        UpdateRepairOrderRequest::class => [
            UpdateRepairOrderRequestLis::class
        ],
        UpdateRepairProductPart::class => [
            UpdateRepairProductPartLis::class
        ],
        CreateSalesAccount::class => [
            CreateSalesAccountLis::class
        ],  
        CreateSalesCall::class => [
            CreateSalesCallLis::class
        ],
        CreateSalesContact::class => [
            CreateSalesContactLis::class
        ],
        CreateSalesDocument::class => [
            CreateSalesDocumentLis::class
        ],        
        CreateSalesInvoice::class => [
            CreateSalesInvoiceLis::class
        ],
        CreateSalesMeeting::class => [
            CreateSalesMeetingLis::class
        ],
        CreateSalesOpportunity::class => [
            CreateSalesOpportunityLis::class
        ],
        CreateSalesOrder::class => [
            CreateSalesOrderLis::class
        ],
        CreateSalesQuote::class => [
            CreateSalesQuoteLis::class
        ],
        UpdateSalesAccount::class => [
            UpdateSalesAccountLis::class
        ],  
        UpdateSalesCall::class => [
            UpdateSalesCallLis::class
        ],
        UpdateSalesContact::class => [
            UpdateSalesContactLis::class
        ],
        UpdateSalesDocument::class => [
            UpdateSalesDocumentLis::class
        ],        
        UpdateSalesInvoice::class => [
            UpdateSalesInvoiceLis::class
        ],
        UpdateSalesMeeting::class => [
            UpdateSalesMeetingLis::class
        ],
        UpdateSalesOpportunity::class => [
            UpdateSalesOpportunityLis::class
        ],
        UpdateSalesOrder::class => [
            UpdateSalesOrderLis::class
        ],
        UpdateSalesQuote::class => [
            UpdateSalesQuoteLis::class
        ],  
        CreateProject::class => [
            CreateProjectLis::class
        ],  
        CreateProjectMilestone::class => [
            CreateProjectMilestoneLis::class
        ],  
        CreateProjectTask::class => [
            CreateProjectTaskLis::class
        ],  
        ProjectShareToClient::class => [
            ProjectShareToClientLis::class
        ],  
        UpdateProject::class => [
            UpdateProjectLis::class
        ],  
        UpdateProjectMilestone::class => [
            UpdateProjectMilestoneLis::class
        ],  
        UpdateProjectTask::class => [
            UpdateProjectTaskLis::class
        ],
        CreateVisitor::class => [
            CreateVisitorLis::class
        ], 
        CreateVisitorLog::class => [
            CreateVisitorLogLis::class
        ], 
        UpdateVisitor::class => [
            UpdateVisitorLis::class
        ], 
        UpdateVisitorLog::class => [
            UpdateVisitorLogLis::class
        ],  
        
    ];
}
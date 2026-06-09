<?php

namespace Workdo\SMS\Providers;

use App\Events\CreateUser;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\ProductService\Events\CreateProductServiceItem;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Events\CreateVendor;
use Workdo\Lead\Events\CreateLead;
use Workdo\Lead\Events\CreateDeal;
use Workdo\Lead\Events\LeadMoved;
use Workdo\Lead\Events\DealMoved;
use Workdo\Lead\Events\LeadConvertDeal;
use Workdo\Pos\Events\CreatePos;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateProjectTask;
use Workdo\Taskly\Events\CreateProjectBug;
use Workdo\Taskly\Events\CreateProjectMilestone;
use Workdo\Taskly\Events\UpdateTaskStage;
use Workdo\Appointment\Events\AppointmentStatus;
use Workdo\Appointment\Events\CreateSchedule;
use Workdo\SMS\Listeners\CreateUserLis;
use Workdo\SMS\Listeners\CreateProductServiceLis;
use Workdo\SMS\Listeners\CreateCustomerLis;
use Workdo\SMS\Listeners\CreateVendorLis;
use Workdo\SMS\Listeners\CreateLeadLis;
use Workdo\SMS\Listeners\CreateDealLis;
use Workdo\SMS\Listeners\LeadMovedLis;
use Workdo\SMS\Listeners\DealMovedLis;
use Workdo\SMS\Listeners\LeadConvertDealLis;
use Workdo\SMS\Listeners\CreatePosLis;
use Workdo\SMS\Listeners\CreateProjectLis;
use Workdo\SMS\Listeners\CreateProjectTaskLis;
use Workdo\SMS\Listeners\CreateProjectBugLis;
use Workdo\SMS\Listeners\CreateProjectMilestoneLis;
use Workdo\SMS\Listeners\UpdateTaskStageLis;
use Workdo\SMS\Listeners\AppointmentStatusLis;
use Workdo\SMS\Listeners\CreateScheduleLis;
use Workdo\CMMS\Events\CreateWorkRequest;
use Workdo\CMMS\Events\CreateSupplier;
use Workdo\CMMS\Events\CreateCmmsPos;
use Workdo\CMMS\Events\CreateWorkOrder;
use Workdo\CMMS\Events\CreateComponent;
use Workdo\CMMS\Events\CreateLocation;
use Workdo\CMMS\Events\CreatePreventiveMaintenance;
use Workdo\SMS\Listeners\CreateWorkRequestLis;
use Workdo\SMS\Listeners\CreateSupplierLis;
use Workdo\SMS\Listeners\CreateCmmsPosLis;
use Workdo\SMS\Listeners\CreateWorkOrderLis;
use Workdo\SMS\Listeners\CreateComponentLis;
use Workdo\SMS\Listeners\CreateLocationLis;
use Workdo\SMS\Listeners\CreatePreventiveMaintenanceLis;
use Workdo\Contract\Events\CreateContract;
use Workdo\SMS\Listeners\CreateContractLis;
use Workdo\Recruitment\Events\SubmitApplication;
use Workdo\Recruitment\Events\CreateInterview;
use Workdo\Recruitment\Events\ConvertOfferToEmployee;
use Workdo\Recruitment\Events\CreateJobPosting;
use Workdo\SMS\Listeners\SubmitApplicationLis;
use Workdo\SMS\Listeners\CreateInterviewLis;
use Workdo\SMS\Listeners\ConvertOfferToEmployeeLis;
use Workdo\SMS\Listeners\CreateJobPostingLis;
use Workdo\Sales\Events\CreateSalesQuote;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\Sales\Events\CreateSalesMeeting;
use Workdo\SMS\Listeners\CreateSalesQuoteLis;
use Workdo\SMS\Listeners\CreateSalesOrderLis;
use Workdo\SMS\Listeners\CreateSalesInvoicePaymentLis;
use Workdo\SMS\Listeners\CreateSalesMeetingLis;
use Workdo\Spreadsheet\Events\CreateSpreadsheet;
use Workdo\SMS\Listeners\CreateSpreadsheetLis;
use Workdo\Training\Events\CreateTrainer;
use Workdo\SMS\Listeners\CreateTrainerLis;
use Workdo\ZoomMeeting\Events\CreateZoomMeeting;
use Workdo\SMS\Listeners\CreateZoomMeetingLis;
use Workdo\FixEquipment\Events\CreateFixEquipmentAccessory;
use Workdo\FixEquipment\Events\CreateFixEquipmentAsset;
use Workdo\FixEquipment\Events\CreateFixEquipmentAudit;
use Workdo\FixEquipment\Events\CreateFixEquipmentComponent;
use Workdo\FixEquipment\Events\CreateFixEquipmentConsumable;
use Workdo\FixEquipment\Events\CreateFixEquipmentLicense;
use Workdo\FixEquipment\Events\CreateFixEquipmentLocation;
use Workdo\FixEquipment\Events\CreateFixEquipmentMaintenance;
use Workdo\SMS\Listeners\CreateFixEquipmentAccessoryLis;
use Workdo\SMS\Listeners\CreateFixEquipmentAssetLis;
use Workdo\SMS\Listeners\CreateFixEquipmentAuditLis;
use Workdo\SMS\Listeners\CreateFixEquipmentComponentLis;
use Workdo\SMS\Listeners\CreateFixEquipmentConsumableLis;
use Workdo\SMS\Listeners\CreateFixEquipmentLicenseLis;
use Workdo\SMS\Listeners\CreateFixEquipmentLocationLis;
use Workdo\SMS\Listeners\CreateFixEquipmentMaintenanceLis;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\VisitorManagement\Events\CreateVisitPurpose;
use Workdo\SMS\Listeners\CreateVisitorLis;
use Workdo\SMS\Listeners\CreateVisitPurposeLis;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceProduct;
use Workdo\SMS\Listeners\CreateWoocommerceProductLis;
use Workdo\Feedback\Events\CreateTemplate;
use Workdo\Feedback\Events\CreateHistory;
use Workdo\SMS\Listeners\CreateFeedbackTemplateLis;
use Workdo\SMS\Listeners\CreateFeedbackHistoryLis;
use Workdo\CleaningManagement\Events\CreateCleaningTeam;
use Workdo\CleaningManagement\Events\CreateCleaningBooking;
use Workdo\CleaningManagement\Events\CreateCleaningInvoice;
use Workdo\SMS\Listeners\CreateCleaningTeamLis;
use Workdo\SMS\Listeners\CreateCleaningBookingLis;
use Workdo\SMS\Listeners\CreateCleaningInvoiceLis;
use Workdo\Timesheet\Events\CreateTimesheet;
use Workdo\SMS\Listeners\CreateTimesheetLis;
use Workdo\Documents\Events\CreateDocument;
use Workdo\Documents\Events\StatusChangeDocument;
use Workdo\SMS\Listeners\CreateDocumentLis;
use Workdo\SMS\Listeners\StatusChangeDocumentLis;
use Workdo\FormBuilder\Events\CreateForm;
use Workdo\FormBuilder\Events\FormConverted;
use Workdo\SMS\Listeners\CreateFormLis;
use Workdo\SMS\Listeners\FormConvertedLis;
use Workdo\Performance\Events\CreateEmployeeReview;
use Workdo\Performance\Events\CreateEmployeeGoal;
use Workdo\Performance\Events\CreatePerformanceIndicator;
use Workdo\Performance\Events\CreateReviewCycle;
use Workdo\SMS\Listeners\CreateEmployeeReviewLis;
use Workdo\SMS\Listeners\CreateEmployeeGoalLis;
use Workdo\SMS\Listeners\CreatePerformanceIndicatorLis;
use Workdo\SMS\Listeners\CreateReviewCycleLis;
use Workdo\Assets\Events\CreateAsset;
use Workdo\Assets\Events\CreateAssetAssignment;
use Workdo\Assets\Events\CreateAssetMaintenance;
use Workdo\Assets\Events\CreateAssetLocation;
use Workdo\SMS\Listeners\CreateAssetLis;
use Workdo\SMS\Listeners\CreateAssetAssignmentLis;
use Workdo\SMS\Listeners\CreateAssetMaintenanceLis;
use Workdo\SMS\Listeners\CreateAssetLocationLis;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeArticle;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeBook;
use Workdo\SMS\Listeners\CreateInternalknowledgeArticleLis;
use Workdo\SMS\Listeners\CreateInternalknowledgeBookLis;
use Workdo\InnovationCenter\Events\CreateCreativity;
use Workdo\InnovationCenter\Events\CreateChallenge;
use Workdo\InnovationCenter\Events\CreateCategory;
use Workdo\SMS\Listeners\CreateCreativityLis;
use Workdo\SMS\Listeners\CreateChallengeLis;
use Workdo\SMS\Listeners\CreateCategoryLis;
use Workdo\ToDo\Events\CreateToDo;
use Workdo\ToDo\Events\CompleteToDo;
use Workdo\SMS\Listeners\CreateToDoLis;
use Workdo\SMS\Listeners\CompleteToDoLis;
use Workdo\Hrm\Events\CreateAward;
use Workdo\SMS\Listeners\CreateAwardLis;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreateUser::class => [
            CreateUserLis::class,
        ],
        CreateProductServiceItem::class => [
            CreateProductServiceLis::class,
        ],
        CreateCustomer::class => [
            CreateCustomerLis::class,
        ],
        CreateVendor::class => [
            CreateVendorLis::class,
        ],
        CreateLead::class => [
            CreateLeadLis::class,
        ],
        CreateDeal::class => [
            CreateDealLis::class,
        ],
        LeadMoved::class => [
            LeadMovedLis::class,
        ],
        DealMoved::class => [
            DealMovedLis::class,
        ],
        LeadConvertDeal::class => [
            LeadConvertDealLis::class,
        ],
        CreatePos::class => [
            CreatePosLis::class,
        ],
        CreateProject::class => [
            CreateProjectLis::class,
        ],
        CreateProjectTask::class => [
            CreateProjectTaskLis::class,
        ],
        CreateProjectBug::class => [
            CreateProjectBugLis::class,
        ],
        CreateProjectMilestone::class => [
            CreateProjectMilestoneLis::class,
        ],
        UpdateTaskStage::class => [
            UpdateTaskStageLis::class,
        ],
        AppointmentStatus::class => [
            AppointmentStatusLis::class,
        ],
        CreateSchedule::class => [
            CreateScheduleLis::class,
        ],
        CreateCmmsPos::class => [
            CreateCmmsPosLis::class,
        ],
        CreateWorkRequest::class => [
            CreateWorkRequestLis::class,
        ],
        CreateSupplier::class => [
            CreateSupplierLis::class,
        ],
        CreateWorkOrder::class => [
            CreateWorkOrderLis::class,
        ],
        CreateComponent::class => [
            CreateComponentLis::class,
        ],
        CreateLocation::class => [
            CreateLocationLis::class,
        ],
        CreatePreventiveMaintenance::class => [
            CreatePreventiveMaintenanceLis::class,
        ],
        CreateContract::class => [
            CreateContractLis::class,
        ],
        SubmitApplication::class => [
            SubmitApplicationLis::class,
        ],
        CreateInterview::class => [
            CreateInterviewLis::class,
        ],
        ConvertOfferToEmployee::class => [
            ConvertOfferToEmployeeLis::class,
        ],
        CreateJobPosting::class => [
            CreateJobPostingLis::class,
        ],
        CreateSalesQuote::class => [
            CreateSalesQuoteLis::class,
        ],
        CreateSalesOrder::class => [
            CreateSalesOrderLis::class,
        ],
        CreateSalesMeeting::class => [
            CreateSalesMeetingLis::class,
        ],
        CreateSpreadsheet::class => [
            CreateSpreadsheetLis::class,
        ],
        CreateTrainer::class => [
            CreateTrainerLis::class,
        ],
        CreateZoomMeeting::class => [
            CreateZoomMeetingLis::class,
        ],
        CreateFixEquipmentAccessory::class => [
            CreateFixEquipmentAccessoryLis::class,
        ],
        CreateFixEquipmentAsset::class => [
            CreateFixEquipmentAssetLis::class,
        ],
        CreateFixEquipmentAudit::class => [
            CreateFixEquipmentAuditLis::class,
        ],
        CreateFixEquipmentComponent::class => [
            CreateFixEquipmentComponentLis::class,
        ],
        CreateFixEquipmentConsumable::class => [
            CreateFixEquipmentConsumableLis::class,
        ],
        CreateFixEquipmentLicense::class => [
            CreateFixEquipmentLicenseLis::class,
        ],
        CreateFixEquipmentLocation::class => [
            CreateFixEquipmentLocationLis::class,
        ],
        CreateFixEquipmentMaintenance::class => [
            CreateFixEquipmentMaintenanceLis::class,
        ],
        CreateVisitor::class => [
            CreateVisitorLis::class,
        ],
        CreateVisitPurpose::class => [
            CreateVisitPurposeLis::class,
        ],
        CreateWoocommerceProduct::class => [
            CreateWoocommerceProductLis::class,
        ],
        CreateTemplate::class => [
            CreateFeedbackTemplateLis::class,
        ],
        CreateHistory::class => [
            CreateFeedbackHistoryLis::class,
        ],
        CreateCleaningTeam::class => [
            CreateCleaningTeamLis::class,
        ],
        CreateCleaningBooking::class => [
            CreateCleaningBookingLis::class,
        ],
        CreateCleaningInvoice::class => [
            CreateCleaningInvoiceLis::class,
        ],
        CreateTimesheet::class => [
            CreateTimesheetLis::class,
        ],
        CreateDocument::class => [
            CreateDocumentLis::class,
        ],
        StatusChangeDocument::class => [
            StatusChangeDocumentLis::class,
        ],
        CreateForm::class => [
            CreateFormLis::class,
        ],
        FormConverted::class => [
            FormConvertedLis::class,
        ],
        CreateEmployeeReview::class => [
            CreateEmployeeReviewLis::class,
        ],
        CreateEmployeeGoal::class => [
            CreateEmployeeGoalLis::class,
        ],
        CreatePerformanceIndicator::class => [
            CreatePerformanceIndicatorLis::class,
        ],
        CreateReviewCycle::class => [
            CreateReviewCycleLis::class,
        ],
        CreateAsset::class => [
            CreateAssetLis::class,
        ],
        CreateAssetAssignment::class => [
            CreateAssetAssignmentLis::class,
        ],
        CreateAssetMaintenance::class => [
            CreateAssetMaintenanceLis::class,
        ],
        CreateAssetLocation::class => [
            CreateAssetLocationLis::class,
        ],
        CreateInternalknowledgeArticle::class => [
            CreateInternalknowledgeArticleLis::class,
        ],
        CreateInternalknowledgeBook::class => [
            CreateInternalknowledgeBookLis::class,
        ],
        CreateCreativity::class => [
            CreateCreativityLis::class,
        ],
        CreateChallenge::class => [
            CreateChallengeLis::class,
        ],
        CreateCategory::class => [
            CreateCategoryLis::class,
        ],
        CreateToDo::class => [
            CreateToDoLis::class,
        ],
        CompleteToDo::class => [
            CompleteToDoLis::class,
        ],
        CreateAward::class => [
            CreateAwardLis::class,
        ],
    ];
}

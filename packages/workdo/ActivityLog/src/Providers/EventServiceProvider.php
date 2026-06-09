<?php

namespace Workdo\ActivityLog\Providers;

use App\Events\CreateSalesInvoice as EventsCreateSalesInvoice;
use App\Events\CreateUser;
use App\Events\UpdateSalesInvoice;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Events\CreateExpense;
use Workdo\Account\Events\CreateRevenue;
use Workdo\Account\Events\CreateVendor;
use Workdo\Account\Events\UpdateCustomer;
use Workdo\Account\Events\UpdateExpense;
use Workdo\Account\Events\UpdateRevenue;
use Workdo\Account\Events\UpdateVendor;
use Workdo\ActivityLog\Listeners\AcceptSalesRetainerLis;
use Workdo\ActivityLog\Listeners\AppointmentStatusLis;
use Workdo\ActivityLog\Listeners\CreateCustomerLis;
use Workdo\ActivityLog\Listeners\CreateExpenseLis;
use Workdo\ActivityLog\Listeners\CreateRevenueLis;
use Workdo\ActivityLog\Listeners\CreateVendorLis;
use Workdo\ActivityLog\Listeners\UpdateCustomerLis;
use Workdo\ActivityLog\Listeners\UpdateExpenseLis;
use Workdo\ActivityLog\Listeners\UpdateRevenueLis;
use Workdo\ActivityLog\Listeners\UpdateVendorLis;
use Workdo\ActivityLog\Listeners\CreateAllowanceLis;
use Workdo\ActivityLog\Listeners\CreateAppointmentLis;
use Workdo\ActivityLog\Listeners\CreateAttendanceLis;
use Workdo\ActivityLog\Listeners\CreateAwardLis;
use Workdo\ActivityLog\Listeners\CreateChallengeLis;
use Workdo\ActivityLog\Listeners\CreateCleaningBookingLis;
use Workdo\ActivityLog\Listeners\CreateCleaningTeamLis;
use Workdo\ActivityLog\Listeners\CreateCmmsposLis;
use Workdo\ActivityLog\Listeners\CreateComplaintLis;
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
use Workdo\ActivityLog\Listeners\CreateDeductionLis;
use Workdo\ActivityLog\Listeners\CreateEmployeeLis;
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
use Workdo\ActivityLog\Listeners\CreateLeaveApplicationLis;
use Workdo\ActivityLog\Listeners\CreateLoanLis;
use Workdo\ActivityLog\Listeners\CreateMachineInsuranceLis;
use Workdo\ActivityLog\Listeners\CreateMachineLis;
use Workdo\ActivityLog\Listeners\CreateMachineRepairRequestLis;
use Workdo\ActivityLog\Listeners\CreateNoteLis;
use Workdo\ActivityLog\Listeners\CreateOvertimeLis;
use Workdo\ActivityLog\Listeners\CreatePayrollLis;
use Workdo\ActivityLog\Listeners\CreatePolicyLis;
use Workdo\ActivityLog\Listeners\CreatePosLis;
use Workdo\ActivityLog\Listeners\CreateProjectLis;
use Workdo\ActivityLog\Listeners\CreateProjectMilestoneLis;
use Workdo\ActivityLog\Listeners\CreateProjectTaskLis;
use Workdo\ActivityLog\Listeners\CreatePromotionLis;
use Workdo\ActivityLog\Listeners\CreateRepairOrderRequestLis;
use Workdo\ActivityLog\Listeners\CreateRepairProductPartLis;
use Workdo\ActivityLog\Listeners\CreateResignationLis;
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
use Workdo\ActivityLog\Listeners\CreateTerminationLis;
use Workdo\ActivityLog\Listeners\CreateUserLis;
use Workdo\ActivityLog\Listeners\CreateVehicleLis;
use Workdo\ActivityLog\Listeners\CreateVerificationLis;
use Workdo\ActivityLog\Listeners\CreateVisitorLis;
use Workdo\ActivityLog\Listeners\CreateVisitorLogLis;
use Workdo\ActivityLog\Listeners\CreateWarningLis;
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
use Workdo\ActivityLog\Listeners\UpdateAllowanceLis;
use Workdo\ActivityLog\Listeners\UpdateAppointmentLis;
use Workdo\ActivityLog\Listeners\UpdateAttendanceLis;
use Workdo\ActivityLog\Listeners\UpdateAwardLis;
use Workdo\ActivityLog\Listeners\UpdateChallengeLis;
use Workdo\ActivityLog\Listeners\UpdateDealLis;
use Workdo\ActivityLog\Listeners\UpdateCleaningBookingLis;
use Workdo\ActivityLog\Listeners\UpdateCleaningTeamLis;
use Workdo\ActivityLog\Listeners\UpdateCmmsPosLis;
use Workdo\ActivityLog\Listeners\UpdateComplaintLis;
use Workdo\ActivityLog\Listeners\UpdateComponentLis;
use Workdo\ActivityLog\Listeners\UpdateContractLis;
use Workdo\ActivityLog\Listeners\UpdateCreativityLis;
use Workdo\ActivityLog\Listeners\UpdateDealTaskLis;
use Workdo\ActivityLog\Listeners\UpdateDeductionLis;
use Workdo\ActivityLog\Listeners\UpdateDocumentLis;
use Workdo\ActivityLog\Listeners\UpdateEmployeeLis;
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
use Workdo\ActivityLog\Listeners\UpdateLeaveApplicationLis;
use Workdo\ActivityLog\Listeners\UpdateLoanLis;
use Workdo\ActivityLog\Listeners\UpdateLocationLis;
use Workdo\ActivityLog\Listeners\UpdateMachineInsuranceLis;
use Workdo\ActivityLog\Listeners\UpdateMachineLis;
use Workdo\ActivityLog\Listeners\UpdateMachineRepairRequestLis;
use Workdo\ActivityLog\Listeners\UpdateNoteLis;
use Workdo\ActivityLog\Listeners\UpdateOvertimeLis;
use Workdo\ActivityLog\Listeners\UpdatePayrollLis;
use Workdo\ActivityLog\Listeners\UpdatePolicyLis;
use Workdo\ActivityLog\Listeners\UpdatePreventiveMaintenanceLis;
use Workdo\ActivityLog\Listeners\UpdateProjectLis;
use Workdo\ActivityLog\Listeners\UpdateProjectMilestoneLis;
use Workdo\ActivityLog\Listeners\UpdateProjectTaskLis;
use Workdo\ActivityLog\Listeners\UpdatePromotionLis;
use Workdo\ActivityLog\Listeners\UpdateQuestionLis;
use Workdo\ActivityLog\Listeners\UpdateRepairOrderRequestLis;
use Workdo\ActivityLog\Listeners\UpdateRepairProductPartLis;
use Workdo\ActivityLog\Listeners\UpdateResignationLis;
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
use Workdo\ActivityLog\Listeners\UpdateTerminationLis;
use Workdo\ActivityLog\Listeners\UpdateVehicleLis;
use Workdo\ActivityLog\Listeners\UpdateVerificationLis;
use Workdo\ActivityLog\Listeners\UpdateVisitorLis;
use Workdo\ActivityLog\Listeners\UpdateVisitorLogLis;
use Workdo\ActivityLog\Listeners\BookingAppointmentPaymentsLis;
use Workdo\ActivityLog\Listeners\CreateBookingAppointmentLis;
use Workdo\ActivityLog\Listeners\UpdateBookingAppointmentLis;
use Workdo\ActivityLog\Listeners\CreateBookingStaffLis;
use Workdo\ActivityLog\Listeners\UpdateBookingStaffLis;
use Workdo\ActivityLog\Listeners\CreateBookingPackageLis;
use Workdo\ActivityLog\Listeners\UpdateBookingPackageLis;
use Workdo\ActivityLog\Listeners\CreateBookingCustomerLis;
use Workdo\ActivityLog\Listeners\UpdateBookingCustomerLis;
use Workdo\ActivityLog\Listeners\CreateBookingExtraServiceLis;
use Workdo\ActivityLog\Listeners\UpdateBookingExtraServiceLis;
use Workdo\ActivityLog\Listeners\CreateGoogleMeetingLis;
use Workdo\ActivityLog\Listeners\UpdateGoogleMeetingLis;
use Workdo\ActivityLog\Listeners\CreateMeetingHubMeetingLis;
use Workdo\ActivityLog\Listeners\UpdateMeetingHubMeetingLis;
use Workdo\ActivityLog\Listeners\CreateMeetingMinuteLis;
use Workdo\ActivityLog\Listeners\UpdateMeetingMinuteTaskLis;
use Workdo\ActivityLog\Listeners\CreateZoomMeetingLis;
use Workdo\ActivityLog\Listeners\UpdateZoomMeetingLis;
use Workdo\ActivityLog\Listeners\CreateToDoLis;
use Workdo\ActivityLog\Listeners\UpdateToDoLis;
use Workdo\ActivityLog\Listeners\CompleteToDoLis;
use Workdo\ActivityLog\Listeners\CreateCallHubCallListLis;
use Workdo\ActivityLog\Listeners\UpdateCallHubCallListLis;
use Workdo\ActivityLog\Listeners\CreateCallHubCallHistoryLis;
use Workdo\ActivityLog\Listeners\CreateBusinessProcessMappingLis;
use Workdo\ActivityLog\Listeners\UpdateBusinessProcessMappingLis;
use Workdo\ActivityLog\Listeners\CreateSpreadsheetLis;
use Workdo\ActivityLog\Listeners\UpdateSpreadsheetLis;
use Workdo\ActivityLog\Listeners\CreateVideoHubVideoLis;
use Workdo\ActivityLog\Listeners\UpdateVideoHubVideoLis;
use Workdo\ActivityLog\Listeners\CreatePortfolioLis;
use Workdo\ActivityLog\Listeners\UpdatePortfolioLis;
use Workdo\ActivityLog\Listeners\CreateRequestLis;
use Workdo\ActivityLog\Listeners\CreateRetainerLis;
use Workdo\ActivityLog\Listeners\CreateRetainerPaymentLis;
use Workdo\ActivityLog\Listeners\UpdateRequestLis;
use Workdo\ActivityLog\Listeners\SubmitPublicFormLis;
use Workdo\ActivityLog\Listeners\CreateVCardBusinessLis;
use Workdo\ActivityLog\Listeners\UpdateVCardBusinessLis;
use Workdo\ActivityLog\Listeners\CreateVCardAppointmentLis;
use Workdo\ActivityLog\Listeners\CreateVCardContactLis;
use Workdo\ActivityLog\Listeners\SentSalesRetainerLis;
use Workdo\ActivityLog\Listeners\UpdateRetainerLis;
use Workdo\ActivityLog\Listeners\UpdateWarningLis;
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
use Workdo\Hrm\Events\CreateAllowance;
use Workdo\Hrm\Events\CreateAttendance;
use Workdo\Hrm\Events\CreateAward;
use Workdo\Hrm\Events\CreateComplaint;
use Workdo\Hrm\Events\CreateDeduction;
use Workdo\Hrm\Events\CreateEmployee;
use Workdo\Hrm\Events\CreateLeaveApplication;
use Workdo\Hrm\Events\CreateLoan;
use Workdo\Hrm\Events\CreateOverTime;
use Workdo\Hrm\Events\CreatePayroll;
use Workdo\Hrm\Events\CreatePromotion;
use Workdo\Hrm\Events\CreateResignation;
use Workdo\Hrm\Events\CreateTermination;
use Workdo\Hrm\Events\CreateWarning;
use Workdo\Hrm\Events\UpdateAllowance;
use Workdo\Hrm\Events\UpdateAttendance;
use Workdo\Hrm\Events\UpdateAward;
use Workdo\Hrm\Events\UpdateComplaint;
use Workdo\Hrm\Events\UpdateDeduction;
use Workdo\Hrm\Events\UpdateEmployee;
use Workdo\Hrm\Events\UpdateLeaveApplication;
use Workdo\Hrm\Events\UpdateLoan;
use Workdo\Hrm\Events\UpdateOverTime;
use Workdo\Hrm\Events\UpdatePayroll;
use Workdo\Hrm\Events\UpdatePromotion;
use Workdo\Hrm\Events\UpdateResignation;
use Workdo\Hrm\Events\UpdateTermination;
use Workdo\Hrm\Events\UpdateWarning;
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
use Workdo\Sales\Events\CreateSalesMeeting;
use Workdo\Sales\Events\CreateSalesOpportunity;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\Sales\Events\CreateSalesQuote;
use Workdo\Sales\Events\UpdateSalesAccount;
use Workdo\Sales\Events\UpdateSalesCall;
use Workdo\Sales\Events\UpdateSalesContact;
use Workdo\Sales\Events\UpdateSalesDocument;
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
use Workdo\Bookings\Events\BookingAppointmentPayments;
use Workdo\Bookings\Events\CreateBookingAppointment;
use Workdo\Bookings\Events\UpdateBookingAppointment;
use Workdo\Bookings\Events\CreateBookingStaff;
use Workdo\Bookings\Events\UpdateBookingStaff;
use Workdo\Bookings\Events\CreateBookingPackage;
use Workdo\Bookings\Events\UpdateBookingPackage;
use Workdo\Bookings\Events\CreateBookingCustomer;
use Workdo\Bookings\Events\UpdateBookingCustomer;
use Workdo\Bookings\Events\CreateBookingExtraService;
use Workdo\Bookings\Events\UpdateBookingExtraService;
use Workdo\GoogleMeet\Events\CreateGoogleMeeting;
use Workdo\GoogleMeet\Events\UpdateGoogleMeeting;
use Workdo\MeetingHub\Events\CreateMeeting;
use Workdo\MeetingHub\Events\UpdateMeeting;
use Workdo\MeetingHub\Events\CreateMeetingMinute;
use Workdo\MeetingHub\Events\UpdateMeetingMinuteTask;
use Workdo\ZoomMeeting\Events\CreateZoomMeeting;
use Workdo\ZoomMeeting\Events\UpdateZoomMeeting;
use Workdo\ToDo\Events\CreateToDo;
use Workdo\ToDo\Events\UpdateToDo;
use Workdo\ToDo\Events\CompleteToDo;
use Workdo\CallHub\Events\CreateCallHubCallList;
use Workdo\CallHub\Events\UpdateCallHubCallList;
use Workdo\CallHub\Events\CreateCallHubCallHistory;
use Workdo\BusinessProcessMapping\Events\CreateBusinessProcessMapping;
use Workdo\BusinessProcessMapping\Events\UpdateBusinessProcessMapping;
use Workdo\Spreadsheet\Events\CreateSpreadsheet;
use Workdo\Spreadsheet\Events\UpdateSpreadsheet;
use Workdo\VideoHub\Events\CreateVideo;
use Workdo\VideoHub\Events\UpdateVideo;
use Workdo\Portfolio\Events\CreatePortfolio;
use Workdo\Portfolio\Events\UpdatePortfolio;
use Workdo\Requests\Events\CreateRequest;
use Workdo\Requests\Events\UpdateRequest;
use Workdo\Requests\Events\SubmitPublicForm;
use Workdo\Retainer\Events\AcceptSalesRetainer;
use Workdo\Retainer\Events\CreateRetainer;
use Workdo\Retainer\Events\CreateRetainerPayment;
use Workdo\Retainer\Events\SentSalesRetainer;
use Workdo\Retainer\Events\UpdateRetainer;
use Workdo\VCard\Events\CreateBusiness;
use Workdo\VCard\Events\UpdateBusiness;
use Workdo\VCard\Events\CreateAppointment as VCardCreateAppointment;
use Workdo\VCard\Events\CreateContact;
use Workdo\Recruitment\Events\CreateCandidate;
use Workdo\Recruitment\Events\UpdateCandidate;
use Workdo\Recruitment\Events\CreateJobPosting;
use Workdo\Recruitment\Events\UpdateJobPosting;
use Workdo\Recruitment\Events\CreateInterview;
use Workdo\Recruitment\Events\UpdateInterview;
use Workdo\Recruitment\Events\CreateOffer;
use Workdo\Recruitment\Events\UpdateOffer;
use Workdo\Recruitment\Events\ConvertOfferToEmployee;
use Workdo\Recruitment\Events\CreateCandidateOnboarding;
use Workdo\Recruitment\Events\UpdateCandidateOnboarding;
use Workdo\Recruitment\Events\CreateInterviewFeedback;
use Workdo\Recruitment\Events\UpdateInterviewFeedback;
use Workdo\Recruitment\Events\CreateCandidateAssessment;
use Workdo\Recruitment\Events\UpdateCandidateAssessment;
use Workdo\Quotation\Events\CreateQuotation;
use Workdo\Quotation\Events\UpdateQuotation;
use Workdo\Quotation\Events\AcceptSalesQuotation;
use Workdo\Quotation\Events\SentSalesQuotation;
use Workdo\Quotation\Events\ConvertSalesQuotation;
use Workdo\Quotation\Events\RejectSalesQuotation;
use Workdo\ActivityLog\Listeners\CreateCandidateLis;
use Workdo\ActivityLog\Listeners\UpdateCandidateLis;
use Workdo\ActivityLog\Listeners\CreateJobPostingLis;
use Workdo\ActivityLog\Listeners\UpdateJobPostingLis;
use Workdo\ActivityLog\Listeners\CreateInterviewLis;
use Workdo\ActivityLog\Listeners\UpdateInterviewLis;
use Workdo\ActivityLog\Listeners\CreateOfferLis;
use Workdo\ActivityLog\Listeners\UpdateOfferLis;
use Workdo\ActivityLog\Listeners\ConvertOfferToEmployeeLis;
use Workdo\ActivityLog\Listeners\CreateCandidateOnboardingLis;
use Workdo\ActivityLog\Listeners\UpdateCandidateOnboardingLis;
use Workdo\ActivityLog\Listeners\CreateInterviewFeedbackLis;
use Workdo\ActivityLog\Listeners\UpdateInterviewFeedbackLis;
use Workdo\ActivityLog\Listeners\CreateCandidateAssessmentLis;
use Workdo\ActivityLog\Listeners\UpdateCandidateAssessmentLis;
use Workdo\ActivityLog\Listeners\CreateQuotationLis;
use Workdo\ActivityLog\Listeners\UpdateQuotationLis;
use Workdo\ActivityLog\Listeners\AcceptSalesQuotationLis;
use Workdo\ActivityLog\Listeners\SentSalesQuotationLis;
use Workdo\ActivityLog\Listeners\ConvertSalesQuotationLis;
use Workdo\ActivityLog\Listeners\RejectSalesQuotationLis;
use Workdo\Performance\Events\CreateEmployeeGoal;
use Workdo\Performance\Events\UpdateEmployeeGoal;
use Workdo\Performance\Events\CreateEmployeeReview;
use Workdo\Performance\Events\UpdateEmployeeReview;
use Workdo\Performance\Events\CreateReviewCycle;
use Workdo\Performance\Events\UpdateReviewCycle;
use Workdo\ActivityLog\Listeners\CreatePerformanceEmployeeGoalLis;
use Workdo\ActivityLog\Listeners\UpdatePerformanceEmployeeGoalLis;
use Workdo\ActivityLog\Listeners\CreatePerformanceEmployeeReviewLis;
use Workdo\ActivityLog\Listeners\UpdatePerformanceEmployeeReviewLis;
use Workdo\ActivityLog\Listeners\CreatePerformanceReviewCycleLis;
use Workdo\ActivityLog\Listeners\UpdatePerformanceReviewCycleLis;
use Workdo\Training\Events\CreateTraining;
use Workdo\Training\Events\UpdateTraining;
use Workdo\Training\Events\CreateTrainingTask;
use Workdo\Training\Events\UpdateTrainingTask;
use Workdo\Training\Events\CreateTrainingFeedback;
use Workdo\Training\Events\CreateTrainer;
use Workdo\Training\Events\UpdateTrainer;
use Workdo\ActivityLog\Listeners\CreateTrainingLis;
use Workdo\ActivityLog\Listeners\UpdateTrainingLis;
use Workdo\ActivityLog\Listeners\CreateTrainingTaskLis;
use Workdo\ActivityLog\Listeners\UpdateTrainingTaskLis;
use Workdo\ActivityLog\Listeners\CreateTrainingFeedbackLis;
use Workdo\ActivityLog\Listeners\CreateTrainerLis;
use Workdo\ActivityLog\Listeners\UpdateTrainerLis;
use Workdo\LMS\Events\CreateCourse;
use Workdo\LMS\Events\UpdateCourse;
use Workdo\LMS\Events\CreateLMSStudent;
use Workdo\LMS\Events\UpdateStudent;
use Workdo\LMS\Events\CreateOrder;
use Workdo\LMS\Events\UpdateOrder;
use Workdo\LMS\Events\CreateCategory;
use Workdo\LMS\Events\UpdateCategory;
use Workdo\LMS\Events\CreateCoupon;
use Workdo\LMS\Events\UpdateCoupon;
use Workdo\ActivityLog\Listeners\CreateLMSCourseLis;
use Workdo\ActivityLog\Listeners\UpdateLMSCourseLis;
use Workdo\ActivityLog\Listeners\CreateLMSStudentLis;
use Workdo\ActivityLog\Listeners\UpdateLMSStudentLis;
use Workdo\ActivityLog\Listeners\CreateLMSOrderLis;
use Workdo\ActivityLog\Listeners\UpdateLMSOrderLis;
use Workdo\ActivityLog\Listeners\CreateLMSCategoryLis;
use Workdo\ActivityLog\Listeners\UpdateLMSCategoryLis;
use Workdo\ActivityLog\Listeners\CreateLMSCouponLis;
use Workdo\ActivityLog\Listeners\UpdateLMSCouponLis;
use Workdo\TeamWorkload\Events\CreateTeamWorkloadHoliday;
use Workdo\TeamWorkload\Events\UpdateTeamWorkloadHoliday;
use Workdo\TeamWorkload\Events\CreateTeamWorkloadTimesheet;
use Workdo\TeamWorkload\Events\UpdateTeamWorkloadTimesheet;
use Workdo\TimeTracker\Events\CreateTimeTracker;
use Workdo\TimeTracker\Events\UpdateTimeTracker;
use Workdo\MarketingPlan\Events\CreateMarketingPlan;
use Workdo\MarketingPlan\Events\UpdateMarketingPlan;
use Workdo\ActivityLog\Listeners\CreateMarketingPlanLis;
use Workdo\ActivityLog\Listeners\UpdateMarketingPlanLis;
use Workdo\Planning\Events\CreatePlanningCharter;
use Workdo\Planning\Events\UpdatePlanningCharter;
use Workdo\Planning\Events\CreatePlanningCharterComment;
use Workdo\Planning\Events\CreatePlanningChallenge;
use Workdo\Planning\Events\UpdatePlanningChallenge;
use Workdo\ActivityLog\Listeners\CreateTimeTrackerLis;
use Workdo\ActivityLog\Listeners\UpdateTimeTrackerLis;
use Workdo\ActivityLog\Listeners\CreatePlanningCharterLis;
use Workdo\ActivityLog\Listeners\UpdatePlanningCharterLis;
use Workdo\ActivityLog\Listeners\CreatePlanningCharterCommentLis;
use Workdo\ActivityLog\Listeners\CreatePlanningChallengeLis;
use Workdo\ActivityLog\Listeners\UpdatePlanningChallengeLis;
use Workdo\ActivityLog\Listeners\CreateTeamWorkloadHolidayLis;
use Workdo\ActivityLog\Listeners\UpdateTeamWorkloadHolidayLis;
use Workdo\ActivityLog\Listeners\CreateTeamWorkloadTimesheetLis;
use Workdo\ActivityLog\Listeners\UpdateTeamWorkloadTimesheetLis;
use Workdo\Commission\Events\CreateCommissionPlan;
use Workdo\Commission\Events\UpdateCommissionPlan;
use Workdo\Commission\Events\CreateCommissionPayment;
use Workdo\Commission\Events\UpdateCommissionPaymentStatus;
use Workdo\Commission\Events\CommissionReceiptStatus;
use Workdo\ActivityLog\Listeners\CreateCommissionPlanLis;
use Workdo\ActivityLog\Listeners\UpdateCommissionPlanLis;
use Workdo\ActivityLog\Listeners\CreateCommissionPaymentLis;
use Workdo\ActivityLog\Listeners\UpdateCommissionPaymentStatusLis;
use Workdo\ActivityLog\Listeners\CommissionReceiptStatusLis;
use Workdo\SupportTicket\Events\CreateTicket;
use Workdo\SupportTicket\Events\CreateTicketConversion;
use Workdo\SupportTicket\Events\CreateContact as SupportTicketCreateContact;
use Workdo\ActivityLog\Listeners\CreateTicketLis;
use Workdo\ActivityLog\Listeners\CreateTicketConversionLis;
use Workdo\ActivityLog\Listeners\CreateContactLis;
use Workdo\Inventory\Events\CreateInventoryAdjustment;
use Workdo\Inventory\Events\ApproveInventoryAdjustment;
use Workdo\Inventory\Events\PostInventoryAdjustment;
use Workdo\ActivityLog\Listeners\CreateInventoryAdjustmentLis;
use Workdo\ActivityLog\Listeners\ApproveInventoryAdjustmentLis;
use Workdo\ActivityLog\Listeners\PostInventoryAdjustmentLis;
use Workdo\Goal\Events\CreateGoal;
use Workdo\Goal\Events\UpdateGoal;
use Workdo\Goal\Events\CreateGoalMilestone;
use Workdo\Goal\Events\UpdateGoalMilestone;
use Workdo\Goal\Events\CreateGoalContribution;
use Workdo\Goal\Events\CreateGoalTracking;
use Workdo\Goal\Events\UpdateGoalTracking;
use Workdo\ActivityLog\Listeners\CreateGoalLis;
use Workdo\ActivityLog\Listeners\UpdateGoalLis;
use Workdo\ActivityLog\Listeners\CreateGoalMilestoneLis;
use Workdo\ActivityLog\Listeners\UpdateGoalMilestoneLis;
use Workdo\ActivityLog\Listeners\CreateGoalContributionLis;
use Workdo\ActivityLog\Listeners\CreateGoalTrackingLis;
use Workdo\ActivityLog\Listeners\UpdateGoalTrackingLis;
use Workdo\BudgetPlanner\Events\CreateBudget;
use Workdo\BudgetPlanner\Events\UpdateBudget;
use Workdo\BudgetPlanner\Events\ApproveBudget;
use Workdo\BudgetPlanner\Events\ActiveBudget;
use Workdo\BudgetPlanner\Events\CloseBudget;
use Workdo\BudgetPlanner\Events\CreateBudgetPeriod;
use Workdo\BudgetPlanner\Events\ApproveBudgetPeriod;
use Workdo\BudgetPlanner\Events\CreateBudgetAllocation;
use Workdo\BudgetPlanner\Events\UpdateBudgetAllocation;
use Workdo\ActivityLog\Listeners\CreateBudgetLis;
use Workdo\ActivityLog\Listeners\UpdateBudgetLis;
use Workdo\ActivityLog\Listeners\ApproveBudgetLis;
use Workdo\ActivityLog\Listeners\ActiveBudgetLis;
use Workdo\ActivityLog\Listeners\CloseBudgetLis;
use Workdo\ActivityLog\Listeners\CreateBudgetPeriodLis;
use Workdo\ActivityLog\Listeners\ApproveBudgetPeriodLis;
use Workdo\ActivityLog\Listeners\CreateBudgetAllocationLis;
use Workdo\ActivityLog\Listeners\UpdateBudgetAllocationLis;
use Workdo\SalesAgent\Events\CreateSalesAgent;
use Workdo\SalesAgent\Events\UpdateSalesAgent;
use Workdo\SalesAgent\Events\CreateSalesAgentCommissionPlan;
use Workdo\SalesAgent\Events\UpdateSalesAgentCommissionPlan;
use Workdo\SalesAgent\Events\CreateSalesTarget;
use Workdo\SalesAgent\Events\UpdateSalesTarget;
use Workdo\SalesAgent\Events\CreateSalesAgentCommissionPayment;
use Workdo\SalesAgent\Events\UpdateSalesAgentCommissionPaymentStatus;
use Workdo\SalesAgent\Events\CreateSalesAgentCommissionAdjustment;
use Workdo\SalesAgent\Events\ApproveSalesAgentCommissionAdjustment;
use Workdo\ActivityLog\Listeners\CreateSalesAgentLis;
use Workdo\ActivityLog\Listeners\UpdateSalesAgentLis;
use Workdo\ActivityLog\Listeners\CreateSalesAgentCommissionPlanLis;
use Workdo\ActivityLog\Listeners\UpdateSalesAgentCommissionPlanLis;
use Workdo\ActivityLog\Listeners\CreateSalesTargetLis;
use Workdo\ActivityLog\Listeners\UpdateSalesTargetLis;
use Workdo\ActivityLog\Listeners\CreateSalesAgentCommissionPaymentLis;
use Workdo\ActivityLog\Listeners\UpdateSalesAgentCommissionPaymentStatusLis;
use Workdo\ActivityLog\Listeners\CreateSalesAgentCommissionAdjustmentLis;
use Workdo\ActivityLog\Listeners\ApproveSalesAgentCommissionAdjustmentLis;
use Workdo\Holidayz\Events\CreateHolidayzRoomBooking;
use Workdo\Holidayz\Events\UpdateHolidayzRoomBooking;
use Workdo\Holidayz\Events\ApproveHolidayzRoomBooking;
use Workdo\Holidayz\Events\HolidayzBookingPayments;
use Workdo\Holidayz\Events\CreateHolidayzHotelCustomer;
use Workdo\Holidayz\Events\UpdateHolidayzHotelCustomer;
use Workdo\Holidayz\Events\CreateHolidayzRoom;
use Workdo\Holidayz\Events\UpdateHolidayzRoom;
use Workdo\Holidayz\Events\CreateHolidayzCoupon;
use Workdo\Holidayz\Events\UpdateHolidayzCoupon;
use Workdo\ActivityLog\Listeners\CreateHolidayzRoomBookingLis;
use Workdo\ActivityLog\Listeners\UpdateHolidayzRoomBookingLis;
use Workdo\ActivityLog\Listeners\ApproveHolidayzRoomBookingLis;
use Workdo\ActivityLog\Listeners\BeautyBookingPaymentsLis;
use Workdo\ActivityLog\Listeners\CreateBeautyBookingLis;
use Workdo\ActivityLog\Listeners\CreateBeautyGiftCardLis;
use Workdo\ActivityLog\Listeners\CreateBeautyMembershipLis;
use Workdo\ActivityLog\Listeners\CreateBeautyServiceLis;
use Workdo\ActivityLog\Listeners\CreateBeautyServiceOfferLis;
use Workdo\ActivityLog\Listeners\HolidayzBookingPaymentsLis;
use Workdo\ActivityLog\Listeners\CreateHolidayzHotelCustomerLis;
use Workdo\ActivityLog\Listeners\UpdateHolidayzHotelCustomerLis;
use Workdo\ActivityLog\Listeners\CreateHolidayzRoomLis;
use Workdo\ActivityLog\Listeners\UpdateHolidayzRoomLis;
use Workdo\ActivityLog\Listeners\CreateHolidayzCouponLis;
use Workdo\ActivityLog\Listeners\UpdateHolidayzCouponLis;
use Workdo\Rotas\Events\CreateRota;
use Workdo\Rotas\Events\UpdateRota;
use Workdo\Rotas\Events\CreateEmployee as RotasCreateEmployee;
use Workdo\Rotas\Events\UpdateEmployee as RotasUpdateEmployee;
use Workdo\Rotas\Events\CreateLeaveApplication as RotasCreateLeaveApplication;
use Workdo\Rotas\Events\UpdateLeaveApplication as RotasUpdateLeaveApplication;
use Workdo\Rotas\Events\UpdateLeaveStatus;
use Workdo\Rotas\Events\CreateAvailability;
use Workdo\Rotas\Events\UpdateAvailability;
use Workdo\Rotas\Events\CreateShift;
use Workdo\Rotas\Events\UpdateShift;
use Workdo\ActivityLog\Listeners\CreateRotaLis;
use Workdo\ActivityLog\Listeners\UpdateRotaLis;
use Workdo\ActivityLog\Listeners\CreateRotasEmployeeLis;
use Workdo\ActivityLog\Listeners\UpdateRotasEmployeeLis;
use Workdo\ActivityLog\Listeners\CreateRotasLeaveApplicationLis;
use Workdo\ActivityLog\Listeners\UpdateRotasLeaveApplicationLis;
use Workdo\ActivityLog\Listeners\UpdateRotasLeaveStatusLis;
use Workdo\ActivityLog\Listeners\CreateRotasAvailabilityLis;
use Workdo\ActivityLog\Listeners\UpdateRotasAvailabilityLis;
use Workdo\ActivityLog\Listeners\CreateRotasShiftLis;
use Workdo\ActivityLog\Listeners\UpdateRotasShiftLis;
use Workdo\PropertyManagement\Events\CreateProperty;
use Workdo\PropertyManagement\Events\UpdateProperty;
use Workdo\PropertyManagement\Events\CreatePropertyTenant;
use Workdo\PropertyManagement\Events\UpdatePropertyTenant;
use Workdo\PropertyManagement\Events\CreatePropertyInvoice;
use Workdo\PropertyManagement\Events\UpdatePropertyInvoice;
use Workdo\PropertyManagement\Events\CreatePropertyPayment;
use Workdo\PropertyManagement\Events\UpdatePropertyPaymentStatus;
use Workdo\PropertyManagement\Events\CreatePropertyMaintenanceRequest;
use Workdo\PropertyManagement\Events\UpdatePropertyMaintenanceRequest;
use Workdo\PropertyManagement\Events\CreatePropertyUnit;
use Workdo\PropertyManagement\Events\UpdatePropertyUnit;
use Workdo\PropertyManagement\Events\CreatePropertyInspection;
use Workdo\PropertyManagement\Events\UpdatePropertyInspection;
use Workdo\ActivityLog\Listeners\CreatePropertyLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyLis;
use Workdo\ActivityLog\Listeners\CreatePropertyTenantLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyTenantLis;
use Workdo\ActivityLog\Listeners\CreatePropertyInvoiceLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyInvoiceLis;
use Workdo\ActivityLog\Listeners\CreatePropertyPaymentLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyPaymentStatusLis;
use Workdo\ActivityLog\Listeners\CreatePropertyMaintenanceRequestLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyMaintenanceRequestLis;
use Workdo\ActivityLog\Listeners\CreatePropertyUnitLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyUnitLis;
use Workdo\ActivityLog\Listeners\CreatePropertyInspectionLis;
use Workdo\ActivityLog\Listeners\UpdatePropertyInspectionLis;
use Workdo\School\Events\CreateAdmission;
use Workdo\School\Events\UpdateAdmission;
use Workdo\School\Events\CreateStudent as SchoolCreateStudent;
use Workdo\School\Events\UpdateStudent as SchoolUpdateStudent;
use Workdo\School\Events\CreateAttendance as SchoolCreateAttendance;
use Workdo\School\Events\UpdateAttendance as SchoolUpdateAttendance;
use Workdo\School\Events\CreateAssessment;
use Workdo\School\Events\UpdateAssessment;
use Workdo\School\Events\CreateHomework;
use Workdo\School\Events\UpdateHomework;
use Workdo\School\Events\CreateFeeCollection;
use Workdo\School\Events\UpdateFeeCollection;
use Workdo\School\Events\CreateFeeStructure;
use Workdo\School\Events\UpdateFeeStructure;
use Workdo\School\Events\CreateEmployee as SchoolCreateEmployee;
use Workdo\School\Events\UpdateEmployee as SchoolUpdateEmployee;
use Workdo\ActivityLog\Listeners\CreateSchoolAdmissionLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolAdmissionLis;
use Workdo\ActivityLog\Listeners\CreateSchoolStudentLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolStudentLis;
use Workdo\ActivityLog\Listeners\CreateSchoolAttendanceLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolAttendanceLis;
use Workdo\ActivityLog\Listeners\CreateSchoolAssessmentLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolAssessmentLis;
use Workdo\ActivityLog\Listeners\CreateSchoolHomeworkLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolHomeworkLis;
use Workdo\ActivityLog\Listeners\CreateSchoolFeeCollectionLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolFeeCollectionLis;
use Workdo\ActivityLog\Listeners\CreateSchoolFeeStructureLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolFeeStructureLis;
use Workdo\ActivityLog\Listeners\CreateSchoolEmployeeLis;
use Workdo\ActivityLog\Listeners\MarkBeautyBookingPaymentPaidLis;
use Workdo\ActivityLog\Listeners\UpdateBeautyBookingLis;
use Workdo\ActivityLog\Listeners\UpdateBeautyGiftCardLis;
use Workdo\ActivityLog\Listeners\UpdateBeautyMembershipLis;
use Workdo\ActivityLog\Listeners\UpdateBeautyServiceLis;
use Workdo\ActivityLog\Listeners\UpdateBeautyServiceOfferLis;
use Workdo\ActivityLog\Listeners\UpdateSchoolEmployeeLis;
use Workdo\ActivityLog\Listeners\CreateBeverageManufacturingLis;
use Workdo\ActivityLog\Listeners\CreateBeverageQualityCheckLis;
use Workdo\ActivityLog\Listeners\CreateBeveragePackagingLis;
use Workdo\ActivityLog\Listeners\CreateBeverageRawMaterialLis;
use Workdo\ActivityLog\Listeners\CompleteBeveragePackagingLis;
use Workdo\ActivityLog\Listeners\CreateBeverageBillOfMaterialLis;
use Workdo\ActivityLog\Listeners\CreateBeverageWasteRecordLis;
use Workdo\ActivityLog\Listeners\CreateCollectionCenterLis;
use Workdo\ActivityLog\Listeners\UpdateBeverageManufacturingLis;
use Workdo\ActivityLog\Listeners\UpdateBeverageQualityCheckLis;
use Workdo\ActivityLog\Listeners\CreateEventLis;
use Workdo\ActivityLog\Listeners\CreateEventBookingLis;
use Workdo\ActivityLog\Listeners\CreateEventBookingPaymentLis;
use Workdo\ActivityLog\Listeners\CancelEventBookingLis;
use Workdo\ActivityLog\Listeners\UpdateEventLis;
use Workdo\ActivityLog\Listeners\UpdateEventBookingPaymentStatusLis;
use Workdo\ActivityLog\Listeners\EventBookingPaymentsLis;
use Workdo\BeautySpaManagement\Events\BeautyBookingPayments;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;
use Workdo\BeautySpaManagement\Events\CreateBeautyGiftCard;
use Workdo\BeautySpaManagement\Events\CreateBeautyMembership;
use Workdo\BeautySpaManagement\Events\CreateBeautyService;
use Workdo\BeautySpaManagement\Events\CreateBeautyServiceOffer;
use Workdo\BeautySpaManagement\Events\MarkBeautyBookingPaymentPaid;
use Workdo\BeautySpaManagement\Events\UpdateBeautyBooking;
use Workdo\BeautySpaManagement\Events\UpdateBeautyGiftCard;
use Workdo\BeautySpaManagement\Events\UpdateBeautyMembership;
use Workdo\BeautySpaManagement\Events\UpdateBeautyService;
use Workdo\BeautySpaManagement\Events\UpdateBeautyServiceOffer;
use Workdo\BeverageManagement\Events\CreateBeverageManufacturing;
use Workdo\BeverageManagement\Events\CreateBeverageQualityCheck;
use Workdo\BeverageManagement\Events\CreateBeveragePackaging;
use Workdo\BeverageManagement\Events\CreateBeverageRawMaterial;
use Workdo\BeverageManagement\Events\CompleteBeveragePackaging;
use Workdo\BeverageManagement\Events\CreateBeverageBillOfMaterial;
use Workdo\BeverageManagement\Events\CreateBeverageWasteRecord;
use Workdo\BeverageManagement\Events\CreateCollectionCenter;
use Workdo\BeverageManagement\Events\UpdateBeverageManufacturing;
use Workdo\BeverageManagement\Events\UpdateBeverageQualityCheck;
use Workdo\EventsManagement\Events\CreateEvent;
use Workdo\EventsManagement\Events\CreateEventBooking;
use Workdo\EventsManagement\Events\CreateEventBookingPayment;
use Workdo\EventsManagement\Events\CancelEventBooking;
use Workdo\EventsManagement\Events\UpdateEvent;
use Workdo\EventsManagement\Events\UpdateEventBookingPaymentStatus;
use Workdo\EventsManagement\Events\EventBookingPayments;

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
        EventsCreateSalesInvoice::class => [
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
        CreateEmployee::class => [
            CreateEmployeeLis::class
        ],
        UpdateEmployee::class => [
            UpdateEmployeeLis::class
        ],
        CreateLeaveApplication::class => [
            CreateLeaveApplicationLis::class
        ],
        UpdateLeaveApplication::class => [
            UpdateLeaveApplicationLis::class
        ],
        CreateAttendance::class => [
            CreateAttendanceLis::class
        ],
        UpdateAttendance::class => [
            UpdateAttendanceLis::class
        ],
        CreateAward::class => [
            CreateAwardLis::class
        ],
        UpdateAward::class => [
            UpdateAwardLis::class
        ],
        CreatePromotion::class => [
            CreatePromotionLis::class
        ],
        UpdatePromotion::class => [
            UpdatePromotionLis::class
        ],
        CreateResignation::class => [
            CreateResignationLis::class
        ],
        UpdateResignation::class => [
            UpdateResignationLis::class
        ],
        CreateTermination::class => [
            CreateTerminationLis::class
        ],
        UpdateTermination::class => [
            UpdateTerminationLis::class
        ],
        CreateWarning::class => [
            CreateWarningLis::class
        ],
        UpdateWarning::class => [
            UpdateWarningLis::class
        ],
        CreateComplaint::class => [
            CreateComplaintLis::class
        ],
        UpdateComplaint::class => [
            UpdateComplaintLis::class
        ],
        CreateAllowance::class => [
            CreateAllowanceLis::class
        ],
        UpdateAllowance::class => [
            UpdateAllowanceLis::class
        ],
        CreateDeduction::class => [
            CreateDeductionLis::class
        ],
        UpdateDeduction::class => [
            UpdateDeductionLis::class
        ],
        CreateLoan::class => [
            CreateLoanLis::class
        ],
        UpdateLoan::class => [
            UpdateLoanLis::class
        ],
        CreateOverTime::class => [
            CreateOvertimeLis::class
        ],
        UpdateOverTime::class => [
            UpdateOvertimeLis::class
        ],
        CreatePayroll::class => [
            CreatePayrollLis::class
        ],
        UpdatePayroll::class => [
            UpdatePayrollLis::class
        ],
        CreateVendor::class => [
            CreateVendorLis::class
        ],
        UpdateVendor::class => [
            UpdateVendorLis::class
        ],
        CreateCustomer::class => [
            CreateCustomerLis::class
        ],
        UpdateCustomer::class => [
            UpdateCustomerLis::class
        ],
        CreateExpense::class => [
            CreateExpenseLis::class
        ],
        UpdateExpense::class => [
            UpdateExpenseLis::class
        ],
        CreateRevenue::class => [
            CreateRevenueLis::class
        ],
        UpdateRevenue::class => [
            UpdateRevenueLis::class
        ],
        CreateBookingAppointment::class => [
            CreateBookingAppointmentLis::class
        ],
        UpdateBookingAppointment::class => [
            UpdateBookingAppointmentLis::class
        ],
        BookingAppointmentPayments::class => [
            BookingAppointmentPaymentsLis::class
        ],
        CreateBookingStaff::class => [
            CreateBookingStaffLis::class
        ],
        UpdateBookingStaff::class => [
            UpdateBookingStaffLis::class
        ],
        CreateBookingPackage::class => [
            CreateBookingPackageLis::class
        ],
        UpdateBookingPackage::class => [
            UpdateBookingPackageLis::class
        ],
        CreateBookingCustomer::class => [
            CreateBookingCustomerLis::class
        ],
        UpdateBookingCustomer::class => [
            UpdateBookingCustomerLis::class
        ],
        CreateBookingExtraService::class => [
            CreateBookingExtraServiceLis::class
        ],
        UpdateBookingExtraService::class => [
            UpdateBookingExtraServiceLis::class
        ],
        CreateGoogleMeeting::class => [
            CreateGoogleMeetingLis::class
        ],
        UpdateGoogleMeeting::class => [
            UpdateGoogleMeetingLis::class
        ],
        CreateMeeting::class => [
            CreateMeetingHubMeetingLis::class
        ],
        UpdateMeeting::class => [
            UpdateMeetingHubMeetingLis::class
        ],
        CreateMeetingMinute::class => [
            CreateMeetingMinuteLis::class
        ],
        UpdateMeetingMinuteTask::class => [
            UpdateMeetingMinuteTaskLis::class
        ],
        CreateZoomMeeting::class => [
            CreateZoomMeetingLis::class
        ],
        UpdateZoomMeeting::class => [
            UpdateZoomMeetingLis::class
        ],
        CreateToDo::class => [
            CreateToDoLis::class
        ],
        UpdateToDo::class => [
            UpdateToDoLis::class
        ],
        CompleteToDo::class => [
            CompleteToDoLis::class
        ],
        CreateCallHubCallList::class => [
            CreateCallHubCallListLis::class
        ],
        UpdateCallHubCallList::class => [
            UpdateCallHubCallListLis::class
        ],
        CreateCallHubCallHistory::class => [
            CreateCallHubCallHistoryLis::class
        ],
        CreateBusinessProcessMapping::class => [
            CreateBusinessProcessMappingLis::class
        ],
        UpdateBusinessProcessMapping::class => [
            UpdateBusinessProcessMappingLis::class
        ],
        CreateSpreadsheet::class => [
            CreateSpreadsheetLis::class
        ],
        UpdateSpreadsheet::class => [
            UpdateSpreadsheetLis::class
        ],
        CreateVideo::class => [
            CreateVideoHubVideoLis::class
        ],
        UpdateVideo::class => [
            UpdateVideoHubVideoLis::class
        ],
        CreatePortfolio::class => [
            CreatePortfolioLis::class
        ],
        UpdatePortfolio::class => [
            UpdatePortfolioLis::class
        ],
        CreateRequest::class => [
            CreateRequestLis::class
        ],
        UpdateRequest::class => [
            UpdateRequestLis::class
        ],
        SubmitPublicForm::class => [
            SubmitPublicFormLis::class
        ],
        CreateBusiness::class => [
            CreateVCardBusinessLis::class
        ],
        UpdateBusiness::class => [
            UpdateVCardBusinessLis::class
        ],
        VCardCreateAppointment::class => [
            CreateVCardAppointmentLis::class
        ],
        CreateContact::class => [
            CreateVCardContactLis::class
        ],
        CreateRetainer::class => [
            CreateRetainerLis::class
        ],
        UpdateRetainer::class => [
            UpdateRetainerLis::class
        ],
        CreateRetainerPayment::class => [
            CreateRetainerPaymentLis::class
        ],
        SentSalesRetainer::class => [
            SentSalesRetainerLis::class
        ],
        AcceptSalesRetainer::class => [
            AcceptSalesRetainerLis::class
        ],
        CreateCandidate::class => [
            CreateCandidateLis::class
        ],
        UpdateCandidate::class => [
            UpdateCandidateLis::class
        ],
        CreateJobPosting::class => [
            CreateJobPostingLis::class
        ],
        UpdateJobPosting::class => [
            UpdateJobPostingLis::class
        ],
        CreateInterview::class => [
            CreateInterviewLis::class
        ],
        UpdateInterview::class => [
            UpdateInterviewLis::class
        ],
        CreateOffer::class => [
            CreateOfferLis::class
        ],
        UpdateOffer::class => [
            UpdateOfferLis::class
        ],
        ConvertOfferToEmployee::class => [
            ConvertOfferToEmployeeLis::class
        ],
        CreateCandidateOnboarding::class => [
            CreateCandidateOnboardingLis::class
        ],
        UpdateCandidateOnboarding::class => [
            UpdateCandidateOnboardingLis::class
        ],
        CreateInterviewFeedback::class => [
            CreateInterviewFeedbackLis::class
        ],
        UpdateInterviewFeedback::class => [
            UpdateInterviewFeedbackLis::class
        ],
        CreateCandidateAssessment::class => [
            CreateCandidateAssessmentLis::class
        ],
        UpdateCandidateAssessment::class => [
            UpdateCandidateAssessmentLis::class
        ],
        CreateQuotation::class => [
            CreateQuotationLis::class
        ],
        UpdateQuotation::class => [
            UpdateQuotationLis::class
        ],
        AcceptSalesQuotation::class => [
            AcceptSalesQuotationLis::class
        ],
        SentSalesQuotation::class => [
            SentSalesQuotationLis::class
        ],
        ConvertSalesQuotation::class => [
            ConvertSalesQuotationLis::class
        ],
        RejectSalesQuotation::class => [
            RejectSalesQuotationLis::class
        ],
        CreateEmployeeGoal::class => [
            CreatePerformanceEmployeeGoalLis::class
        ],
        UpdateEmployeeGoal::class => [
            UpdatePerformanceEmployeeGoalLis::class
        ],
        CreateEmployeeReview::class => [
            CreatePerformanceEmployeeReviewLis::class
        ],
        UpdateEmployeeReview::class => [
            UpdatePerformanceEmployeeReviewLis::class
        ],
        CreateReviewCycle::class => [
            CreatePerformanceReviewCycleLis::class
        ],
        UpdateReviewCycle::class => [
            UpdatePerformanceReviewCycleLis::class
        ],
        CreateTraining::class => [
            CreateTrainingLis::class
        ],
        UpdateTraining::class => [
            UpdateTrainingLis::class
        ],
        CreateTrainingTask::class => [
            CreateTrainingTaskLis::class
        ],
        UpdateTrainingTask::class => [
            UpdateTrainingTaskLis::class
        ],
        CreateTrainingFeedback::class => [
            CreateTrainingFeedbackLis::class
        ],
        CreateTrainer::class => [
            CreateTrainerLis::class
        ],
        UpdateTrainer::class => [
            UpdateTrainerLis::class
        ],
        CreateCourse::class => [
            CreateLMSCourseLis::class
        ],
        UpdateCourse::class => [
            UpdateLMSCourseLis::class
        ],
        CreateLMSStudent::class => [
            CreateLMSStudentLis::class
        ],
        UpdateStudent::class => [
            UpdateLMSStudentLis::class
        ],
        CreateOrder::class => [
            CreateLMSOrderLis::class
        ],
        UpdateOrder::class => [
            UpdateLMSOrderLis::class
        ],
        CreateCategory::class => [
            CreateLMSCategoryLis::class
        ],
        UpdateCategory::class => [
            UpdateLMSCategoryLis::class
        ],
        CreateCoupon::class => [
            CreateLMSCouponLis::class
        ],
        UpdateCoupon::class => [
            UpdateLMSCouponLis::class
        ],
        CreateTimeTracker::class => [
            CreateTimeTrackerLis::class
        ],
        UpdateTimeTracker::class => [
            UpdateTimeTrackerLis::class
        ],
        CreateMarketingPlan::class => [
            CreateMarketingPlanLis::class
        ],
        UpdateMarketingPlan::class => [
            UpdateMarketingPlanLis::class
        ],
        CreatePlanningCharter::class => [
            CreatePlanningCharterLis::class
        ],
        UpdatePlanningCharter::class => [
            UpdatePlanningCharterLis::class
        ],
        CreatePlanningCharterComment::class => [
            CreatePlanningCharterCommentLis::class
        ],
        CreatePlanningChallenge::class => [
            CreatePlanningChallengeLis::class
        ],
        UpdatePlanningChallenge::class => [
            UpdatePlanningChallengeLis::class
        ],
        CreateTeamWorkloadHoliday::class => [
            CreateTeamWorkloadHolidayLis::class
        ],
        UpdateTeamWorkloadHoliday::class => [
            UpdateTeamWorkloadHolidayLis::class
        ],
        CreateTeamWorkloadTimesheet::class => [
            CreateTeamWorkloadTimesheetLis::class
        ],
        UpdateTeamWorkloadTimesheet::class => [
            UpdateTeamWorkloadTimesheetLis::class
        ],
        CreateCommissionPlan::class => [
            CreateCommissionPlanLis::class
        ],
        UpdateCommissionPlan::class => [
            UpdateCommissionPlanLis::class
        ],
        CreateCommissionPayment::class => [
            CreateCommissionPaymentLis::class
        ],
        UpdateCommissionPaymentStatus::class => [
            UpdateCommissionPaymentStatusLis::class
        ],
        CommissionReceiptStatus::class => [
            CommissionReceiptStatusLis::class
        ],
        CreateTicket::class => [
            CreateTicketLis::class
        ],
        CreateTicketConversion::class => [
            CreateTicketConversionLis::class
        ],
        SupportTicketCreateContact::class => [
            CreateContactLis::class
        ],
        CreateInventoryAdjustment::class => [
            CreateInventoryAdjustmentLis::class
        ],
        ApproveInventoryAdjustment::class => [
            ApproveInventoryAdjustmentLis::class
        ],
        PostInventoryAdjustment::class => [
            PostInventoryAdjustmentLis::class
        ],
        CreateGoal::class => [
            CreateGoalLis::class
        ],
        UpdateGoal::class => [
            UpdateGoalLis::class
        ],
        CreateGoalMilestone::class => [
            CreateGoalMilestoneLis::class
        ],
        UpdateGoalMilestone::class => [
            UpdateGoalMilestoneLis::class
        ],
        CreateGoalContribution::class => [
            CreateGoalContributionLis::class
        ],
        CreateGoalTracking::class => [
            CreateGoalTrackingLis::class
        ],
        UpdateGoalTracking::class => [
            UpdateGoalTrackingLis::class
        ],
        CreateBudget::class => [
            CreateBudgetLis::class
        ],
        UpdateBudget::class => [
            UpdateBudgetLis::class
        ],
        ApproveBudget::class => [
            ApproveBudgetLis::class
        ],
        ActiveBudget::class => [
            ActiveBudgetLis::class
        ],
        CloseBudget::class => [
            CloseBudgetLis::class
        ],
        CreateBudgetPeriod::class => [
            CreateBudgetPeriodLis::class
        ],
        ApproveBudgetPeriod::class => [
            ApproveBudgetPeriodLis::class
        ],
        CreateBudgetAllocation::class => [
            CreateBudgetAllocationLis::class
        ],
        UpdateBudgetAllocation::class => [
            UpdateBudgetAllocationLis::class
        ],
        CreateSalesAgent::class => [
            CreateSalesAgentLis::class
        ],
        UpdateSalesAgent::class => [
            UpdateSalesAgentLis::class
        ],
        CreateSalesAgentCommissionPlan::class => [
            CreateSalesAgentCommissionPlanLis::class
        ],
        UpdateSalesAgentCommissionPlan::class => [
            UpdateSalesAgentCommissionPlanLis::class
        ],
        CreateSalesTarget::class => [
            CreateSalesTargetLis::class
        ],
        UpdateSalesTarget::class => [
            UpdateSalesTargetLis::class
        ],
        CreateSalesAgentCommissionPayment::class => [
            CreateSalesAgentCommissionPaymentLis::class
        ],
        UpdateSalesAgentCommissionPaymentStatus::class => [
            UpdateSalesAgentCommissionPaymentStatusLis::class
        ],
        CreateSalesAgentCommissionAdjustment::class => [
            CreateSalesAgentCommissionAdjustmentLis::class
        ],
        ApproveSalesAgentCommissionAdjustment::class => [
            ApproveSalesAgentCommissionAdjustmentLis::class
        ],
        CreateHolidayzRoomBooking::class => [
            CreateHolidayzRoomBookingLis::class
        ],
        UpdateHolidayzRoomBooking::class => [
            UpdateHolidayzRoomBookingLis::class
        ],
        ApproveHolidayzRoomBooking::class => [
            ApproveHolidayzRoomBookingLis::class
        ],
        HolidayzBookingPayments::class => [
            HolidayzBookingPaymentsLis::class
        ],
        CreateHolidayzHotelCustomer::class => [
            CreateHolidayzHotelCustomerLis::class
        ],
        UpdateHolidayzHotelCustomer::class => [
            UpdateHolidayzHotelCustomerLis::class
        ],
        CreateHolidayzRoom::class => [
            CreateHolidayzRoomLis::class
        ],
        UpdateHolidayzRoom::class => [
            UpdateHolidayzRoomLis::class
        ],
        CreateHolidayzCoupon::class => [
            CreateHolidayzCouponLis::class
        ],
        UpdateHolidayzCoupon::class => [
            UpdateHolidayzCouponLis::class
        ],
        CreateRota::class => [
            CreateRotaLis::class
        ],
        UpdateRota::class => [
            UpdateRotaLis::class
        ],
        RotasCreateEmployee::class => [
            CreateRotasEmployeeLis::class
        ],
        RotasUpdateEmployee::class => [
            UpdateRotasEmployeeLis::class
        ],
        RotasCreateLeaveApplication::class => [
            CreateRotasLeaveApplicationLis::class
        ],
        RotasUpdateLeaveApplication::class => [
            UpdateRotasLeaveApplicationLis::class
        ],
        UpdateLeaveStatus::class => [
            UpdateRotasLeaveStatusLis::class
        ],
        CreateAvailability::class => [
            CreateRotasAvailabilityLis::class
        ],
        UpdateAvailability::class => [
            UpdateRotasAvailabilityLis::class
        ],
        CreateShift::class => [
            CreateRotasShiftLis::class
        ],
        UpdateShift::class => [
            UpdateRotasShiftLis::class
        ],
        CreateProperty::class => [
            CreatePropertyLis::class
        ],
        UpdateProperty::class => [
            UpdatePropertyLis::class
        ],
        CreatePropertyTenant::class => [
            CreatePropertyTenantLis::class
        ],
        UpdatePropertyTenant::class => [
            UpdatePropertyTenantLis::class
        ],
        CreatePropertyInvoice::class => [
            CreatePropertyInvoiceLis::class
        ],
        UpdatePropertyInvoice::class => [
            UpdatePropertyInvoiceLis::class
        ],
        CreatePropertyPayment::class => [
            CreatePropertyPaymentLis::class
        ],
        UpdatePropertyPaymentStatus::class => [
            UpdatePropertyPaymentStatusLis::class
        ],
        CreatePropertyMaintenanceRequest::class => [
            CreatePropertyMaintenanceRequestLis::class
        ],
        UpdatePropertyMaintenanceRequest::class => [
            UpdatePropertyMaintenanceRequestLis::class
        ],
        CreatePropertyUnit::class => [
            CreatePropertyUnitLis::class
        ],
        UpdatePropertyUnit::class => [
            UpdatePropertyUnitLis::class
        ],
        CreatePropertyInspection::class => [
            CreatePropertyInspectionLis::class
        ],
        UpdatePropertyInspection::class => [
            UpdatePropertyInspectionLis::class
        ],
        CreateAdmission::class => [
            CreateSchoolAdmissionLis::class
        ],
        UpdateAdmission::class => [
            UpdateSchoolAdmissionLis::class
        ],
        SchoolCreateStudent::class => [
            CreateSchoolStudentLis::class
        ],
        SchoolUpdateStudent::class => [
            UpdateSchoolStudentLis::class
        ],
        SchoolCreateAttendance::class => [
            CreateSchoolAttendanceLis::class
        ],
        SchoolUpdateAttendance::class => [
            UpdateSchoolAttendanceLis::class
        ],
        CreateAssessment::class => [
            CreateSchoolAssessmentLis::class
        ],
        UpdateAssessment::class => [
            UpdateSchoolAssessmentLis::class
        ],
        CreateHomework::class => [
            CreateSchoolHomeworkLis::class
        ],
        UpdateHomework::class => [
            UpdateSchoolHomeworkLis::class
        ],
        CreateFeeCollection::class => [
            CreateSchoolFeeCollectionLis::class
        ],
        UpdateFeeCollection::class => [
            UpdateSchoolFeeCollectionLis::class
        ],
        CreateFeeStructure::class => [
            CreateSchoolFeeStructureLis::class
        ],
        UpdateFeeStructure::class => [
            UpdateSchoolFeeStructureLis::class
        ],
        SchoolCreateEmployee::class => [
            CreateSchoolEmployeeLis::class
        ],
        SchoolUpdateEmployee::class => [
            UpdateSchoolEmployeeLis::class
        ],
        CreateBeautyBooking::class => [
            CreateBeautyBookingLis::class
        ],
        UpdateBeautyBooking::class => [
            UpdateBeautyBookingLis::class
        ],
        BeautyBookingPayments::class => [
            BeautyBookingPaymentsLis::class
        ],
        MarkBeautyBookingPaymentPaid::class => [
            MarkBeautyBookingPaymentPaidLis::class
        ],
        CreateBeautyService::class => [
            CreateBeautyServiceLis::class
        ],
        UpdateBeautyService::class => [
            UpdateBeautyServiceLis::class
        ],
        CreateBeautyMembership::class => [
            CreateBeautyMembershipLis::class
        ],
        UpdateBeautyMembership::class => [
            UpdateBeautyMembershipLis::class
        ],
        CreateBeautyGiftCard::class => [
            CreateBeautyGiftCardLis::class
        ],
        UpdateBeautyGiftCard::class => [
            UpdateBeautyGiftCardLis::class
        ],
        CreateBeautyServiceOffer::class => [
            CreateBeautyServiceOfferLis::class
        ],
        UpdateBeautyServiceOffer::class => [
            UpdateBeautyServiceOfferLis::class
        ],
        CreateBeverageManufacturing::class => [
            CreateBeverageManufacturingLis::class
        ],
        CreateBeverageQualityCheck::class => [
            CreateBeverageQualityCheckLis::class
        ],
        CreateBeveragePackaging::class => [
            CreateBeveragePackagingLis::class
        ],
        CreateBeverageRawMaterial::class => [
            CreateBeverageRawMaterialLis::class
        ],
        CompleteBeveragePackaging::class => [
            CompleteBeveragePackagingLis::class
        ],
        CreateBeverageBillOfMaterial::class => [
            CreateBeverageBillOfMaterialLis::class
        ],
        CreateBeverageWasteRecord::class => [
            CreateBeverageWasteRecordLis::class
        ],
        CreateCollectionCenter::class => [
            CreateCollectionCenterLis::class
        ],
        UpdateBeverageManufacturing::class => [
            UpdateBeverageManufacturingLis::class
        ],
        UpdateBeverageQualityCheck::class => [
            UpdateBeverageQualityCheckLis::class
        ],
        CreateEvent::class => [
            CreateEventLis::class
        ],
        UpdateEvent::class => [
            UpdateEventLis::class
        ],
        CreateEventBooking::class => [
            CreateEventBookingLis::class
        ],
        CreateEventBookingPayment::class => [
            CreateEventBookingPaymentLis::class
        ],
        CancelEventBooking::class => [
            CancelEventBookingLis::class
        ],
        UpdateEventBookingPaymentStatus::class => [
            UpdateEventBookingPaymentStatusLis::class
        ],
        EventBookingPayments::class => [
            EventBookingPaymentsLis::class
        ],
    ];
}

<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getProductionDtHeader($page){

    /* Process Header */
    $data['process'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['process'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Remark"];

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['rejectionComments'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['rejectionComments'][] = ["name"=>"Code"];
    $data['rejectionComments'][] = ["name"=>"Reason"];

    /* Estimation & Design Header */
    $data['estimation'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['estimation'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
	$data['estimation'][] = ["name"=>"Job No."];
	$data['estimation'][] = ["name"=>"Job Date"];
	$data['estimation'][] = ["name"=>"Customer Name"];
	$data['estimation'][] = ["name"=>"Item Name"];
    $data['estimation'][] = ["name"=>"Order Qty"];
    $data['estimation'][] = ["name"=>"Bom Status"];
    $data['estimation'][] = ["name"=>"Priority"];
    $data['estimation'][] = ["name"=>"FAB. PRODUCTION NOTE"];
    $data['estimation'][] = ["name"=>"POWER COATING NOTE"];
    $data['estimation'][] = ["name"=>"ASSEMBALY NOTE"];
    $data['estimation'][] = ["name"=>"GENERAL NOTE"];

    /* Job Card Header */
    $data['jobcard'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['jobcard'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Remark"];
    $data['jobcard'][] = ["name"=>"Last Activity"];


    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center", "srnoPosition" => 0];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"Part Name"];
    $data['productOption'][] = ["name"=>"BOM","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","textAlign"=>"center"]; 
    $data['productOption'][] = ["name"=>"Action","textAlign"=>"center"];

    /** Outsource */
    $data['outsource'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['outsource'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['outsource'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Challan No.", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Prc No.", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Vendor"];
    $data['outsource'][] = ["name" => "Product"];
    $data['outsource'][] = ["name" => "Process"];
    $data['outsource'][] = ["name" => "Batch No"];
    $data['outsource'][] = ["name" => "Challan Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Ok Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Rej. Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "W.P. Return", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Rate", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Approx Rate", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Material Weight", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Material Price", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Pre Process Cost", "textAlign" => "center"];
    $data['outsource'][] = ["name" => "Material Value", "textAlign" => "center"];

    /* Pending Rejection Review */
    $data['pendingReview'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['pendingReview'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"PRC No.","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Product","textAlign"=>"left"];
    $data['pendingReview'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Machine/Vendor","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Operator/Inspector","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['pendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];


    /* Pending Rejection Review */
    $data['rejectionReview'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['rejectionReview'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"PRC No.","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Product","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw Found","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw Belongs To","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['rejectionReview'][] = ["name"=>"Rej/Rw By","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Machine","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Operator","textAlign"=>"left"];
    $data['rejectionReview'][] = ["name"=>"Note","textAlign"=>"left"];

    /*** Cutting Header */
    $data['cutting'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['cutting'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"SO No","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"SO Date","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Plan Qty","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Production Qty","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Batch No","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Cutting Length","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Cutting Dia.","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Cut Weight","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Cut Type","textAlign"=>"center"];
    $data['cutting'][] = ["name"=>"Remark","textAlign"=>"center"];

    /* SOP Header */
    $data['sop'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['sop'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['sop'][] = ["name"=>"Mfg. Type"];
    $data['sop'][] = ["name"=>"PRC No."];
    $data['sop'][] = ["name"=>"PRC Date"];
    $data['sop'][] = ["name"=>"Customer"];
    $data['sop'][] = ["name"=>"SO No"];
    $data['sop'][] = ["name"=>"SO Date"];
    $data['sop'][] = ["name"=>"Cust. PO. No."];
    $data['sop'][] = ["name"=>"Product"];
    $data['sop'][] = ["name"=>"Batch No."];
    $data['sop'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['sop'][] = ["name"=>"Target Date."];
    $data['sop'][] = ["name"=>"Remark"];

    /* SOP Header */
    $data['productionShortage'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['productionShortage'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"SO Number","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Total Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    $data['productionShortage'][] = ["name"=>"WIP Qty"];
    $data['productionShortage'][] = ["name"=>"Production Finished"];
    $data['productionShortage'][] = ["name"=>"RTD Qty"];
    $data['productionShortage'][] = ["name"=>"Shortage Qty"];

    /*** PRC LOG */
    $data['prcLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['prcLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Process From","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Unaccepted","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"In","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Ok","textAlign"=>"center"];
    $data['prcLog'][] = ["name"=>"Rej. Found"];
    $data['prcLog'][] = ["name"=>"Rej."];
    $data['prcLog'][] = ["name"=>"Pending Prod."];
    $data['prcLog'][] = ["name"=>"Stock"];

    /*** Semi Finished LOG */
    $data['semiFinishedLog'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"Inward","textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"Moved","textAlign"=>"center"];
    $data['semiFinishedLog'][] = ["name"=>"Pending","textAlign"=>"center"];

    /** Production Store Demand */
    $data['mfgStoreDemand'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Req No","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Req Date","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Demand","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Issued","textAlign"=>"center"];
    $data['mfgStoreDemand'][] = ["name"=>"Pending","textAlign"=>"center"];

    /** Production Store Request */
    $data['mfgStoreRequest'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Req No","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Req Date","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Request To","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Issued","textAlign"=>"center"];
    $data['mfgStoreRequest'][] = ["name"=>"Pending","textAlign"=>"center"];

    /** Production Store Stock */
    $data['mfgStoreStock'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['mfgStoreStock'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"PRC No","textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"PRC Date","textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"Process From","textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"Type","textAlign"=>"center"];
    $data['mfgStoreStock'][] = ["name"=>"Stock"];

    /* Die Production Header */
    $data['dieProduction'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Job No","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Job Type","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Die Type","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Remark","textAlign"=>"center"];
    $data['dieProduction'][] = ["name"=>"Status","textAlign"=>"center"];

    /* Die Master Header */
    $data['dieMaster'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"Die Type","textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"Die No","textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"Die Run","textAlign"=>"center"];
    $data['dieMaster'][] = ["name"=>"Status","textAlign"=>"center"];
	
	/* Part List Header */
    $data['partList'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['partList'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
	$data['partList'][] = ["name"=>"Die/Punch Code","textAlign"=>"center"];
    $data['partList'][] = ["name"=>"Die Type","textAlign"=>"center"];
    $data['partList'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['partList'][] = ["name"=>"Challan No","textAlign"=>"center"];
	
	/* Die Recut Header */
    $data['dieRecut'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieRecut'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieRecut'][] = ["name"=>"Die No.","textAlign"=>"center"];
    $data['dieRecut'][] = ["name"=>"Description","textAlign"=>"center"];
    $data['dieRecut'][] = ["name"=>"Status","textAlign"=>"center"];
	
	/* Die Outsource Header */
    $chCheckBox = '<input type="checkbox" id="masterChSelect" class="filled-in chk-col-success BulkChallan" value=""><label for="masterChSelect">ALL</label>';
    $data['dieOutsource'][] = ["name" => $chCheckBox,"class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['dieOutsource'][] = ["name"=>"Job No","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Die Type","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieOutsource'][] = ["name"=>"Remark","textAlign"=>"center"];

    /* Die Outsource Challan Header */
    $data['dieOutsourceChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dieOutsourceChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['dieOutsourceChallan'][] = ["name"=>"Challan No"];
	$data['dieOutsourceChallan'][] = ["name"=>"Challan Date"];
	$data['dieOutsourceChallan'][] = ["name"=>"Job No"];
	$data['dieOutsourceChallan'][] = ["name"=>"Die Type"];
	$data['dieOutsourceChallan'][] = ["name"=>"Product"];

    /* Die Scrap Header */
    $data['dieScrap'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"Die Type","textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"Die No","textAlign"=>"center"];    
    $data['dieScrap'][] = ["name"=>"Weight","textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"Material Weight","textAlign"=>"center"];
    $data['dieScrap'][] = ["name"=>"Pending Scrap","textAlign"=>"center"];

    /* GRN Pending Rejection Review */
    $data['grnPendingReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['grnPendingReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['grnPendingReview'][] = ["name"=>"GRN No.","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"GRN Date","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Party","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['grnPendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];

    /* GRN Reviewed Rejection */
    $data['grnRejectionReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['grnRejectionReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['grnRejectionReview'][] = ["name"=>"Source","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"GRN No.","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['grnRejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];

    /* Manual Pending Rejection */
    $data['manualPendingReview'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['manualPendingReview'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['manualPendingReview'][] = ["name"=>"Rej. Date","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Location","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Batch No.","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];
    $data['manualPendingReview'][] = ["name"=>"Pending Qty","textAlign"=>"center"];

    /* Manual Reviewed Rejection */
    $data['manualRejectionReview'][] = ["name" => "Action", "textAlign" => "center","class"=>"no_filter noExport","sortable"=>FALSE];
    $data['manualRejectionReview'][] = ["name"=>"#","textAlign"=>"center","class"=>"no_filter","sortable"=>FALSE];
    $data['manualRejectionReview'][] = ["name"=>"Source","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Location","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Batch No.","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Decision Date","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['manualRejectionReview'][] = ["name"=>"Reviewed Qty","textAlign"=>"center"];

    /* Die Challan Header */
    $data['dieChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['dieChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['dieChallan'][] = ["name"=>"Ch. No."];
    $data['dieChallan'][] = ["name"=>"Ch. Date"];
    //$data['dieChallan'][] = ["name"=>"Ch. Type"];
    $data['dieChallan'][] = ["name"=>"Issue To"];
    $data['dieChallan'][] = ["name"=>"Item"];
    $data['dieChallan'][] = ["name"=>"Set No"];
    $data['dieChallan'][] = ["name"=>"PRC Number"];
    $data['dieChallan'][] = ["name"=>"Remark"];
	
	/* Jobwork Order Header */
    $data['jobworkOrder'][] = ["name"=>"Action","class"=>"text-center no_filter noExport"];
    $data['jobworkOrder'][] = ["name"=>"#","class"=>"text-center no_filter noExport"];
    $data['jobworkOrder'][] = ["name"=>"Order Date"];
    $data['jobworkOrder'][] = ["name"=>"Order No."];
    $data['jobworkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobworkOrder'][] = ["name"=>"Product"];
    $data['jobworkOrder'][] = ["name"=>"Process"];
    $data['jobworkOrder'][] = ["name"=>"Rate/Unit"];
    $data['jobworkOrder'][] = ["name"=>"Rate"];
    $data['jobworkOrder'][] = ["name"=>"Remark"];

    /* Jobwork Bill Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect"><b>ALL</b></label>';
    $data['jwbill'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['jwbill'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['jwbill'][] = ["name"=>$masterCheckBox,"textAlign"=>"center","class"=>"text-center no_filter","orderable"=>"false"];
    $data['jwbill'][] = ["name"=>"In Ch. Date"];
    $data['jwbill'][] = ["name"=>"In Ch. No"];
    $data['jwbill'][] = ["name"=>"Challan No"];
    $data['jwbill'][] = ["name"=>"Vendor"];
	
	/** RM Challan */ 
    $data['rmChallan'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['rmChallan'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['rmChallan'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['rmChallan'][] = ["name" => "Challan No.", "textAlign" => "center"];
    $data['rmChallan'][] = ["name" => "Grn No.", "textAlign" => "center"];
    $data['rmChallan'][] = ["name" => "Vendor"];
    $data['rmChallan'][] = ["name" => "Product"];
    $data['rmChallan'][] = ["name" => "Challan Qty", "textAlign" => "center"];
    $data['rmChallan'][] = ["name" => "Rate", "textAlign" => "center"];
    $data['rmChallan'][] = ["name" => "Approx Rate", "textAlign" => "center"];

    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteButton = $editButton = '';
    if($data->is_system == 0 ){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Process'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editProcess', 'title' : 'Update Process','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }
    
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->remark];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    $rejection_type = ($data->type == 1 ? "Rejection Reason": ($data->type == 2 ? "Idle Reason":"Rework Reason"));

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$rejection_type."'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editRejection', 'title' : 'Update  ".$rejection_type."','call_function':'edit'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->code,$data->remark];
}

function getEstimationData($data){

    $soBomParam = "{'postData':{'trans_main_id' : ".$data->trans_main_id.",'trans_child_id':".$data->trans_child_id."},'modal_id' : 'modal-xxl', 'form_id' : 'addOrderBom', 'fnedit':'orderBom', 'fnsave':'saveOrderBom','title' : 'Order Bom','res_function':'resSaveOrderBom','js_store_fn':'customStore'}";
    $soBom = '<a class="btn btn-info btn-delete permission-write" href="javascript:void(0)" onclick="edit('.$soBomParam.');" datatip="SO Bom" flow="down"><i class="fa fa-database"></i></a>';

    $viewBomParam = "{'postData':{'trans_child_id':".$data->trans_child_id."},'modal_id' : 'modal-xl','fnedit':'viewOrderBom','title' : 'View Bom [Item Name : ".$data->item_name."]','button':'close'}";
    $viewBom = '<a class="btn btn-primary permission-read" href="javascript:void(0)" onclick="edit('.$viewBomParam.');" datatip="View Item Bom" flow="down"><i class="fa fa-eye"></i></a>';

    $reqParam = "{'postData':{'trans_child_id':".$data->trans_child_id.",'trans_number':'".$data->trans_number."','item_name':'".$data->item_name."'},'modal_id' : 'modal-xl', 'form_id' : 'addOrderBom', 'fnedit':'purchaseRequest', 'fnsave':'savePurchaseRequest','title' : 'Send Purchase Request'}";
    $reqButton = '<a class="btn btn-info btn-delete permission-write" href="javascript:void(0)" onclick="edit('.$reqParam.');" datatip="Purchase Request" flow="down"><i class="fa fa-paper-plane"></i></a>';

    $estimationParam = "{'postData':{'id':'".$data->id."','trans_child_id':".$data->trans_child_id.",'trans_main_id':'".$data->trans_main_id."'},'modal_id' : 'modal-xl', 'form_id' : 'estimation', 'fnedit':'addEstimation', 'fnsave':'saveEstimation','title' : 'Estimation & Design'}";
    $estimationButton = '<a class="btn btn-success permission-write" href="javascript:void(0)" onclick="edit('.$estimationParam.');" datatip="Estimation" flow="down"><i class="fa fa-plus"></i></a>';

    if($data->priority == 1):
        $data->priority_status = '<span class="badge badge-pill badge-danger m-1">'.$data->priority_status.'</span>';
    elseif($data->priority == 2):
        $data->priority_status = '<span class="badge badge-pill badge-warning m-1">'.$data->priority_status.'</span>';
    elseif($data->priority == 3):
        $data->priority_status = '<span class="badge badge-pill badge-info m-1">'.$data->priority_status.'</span>';
    endif;

    $data->bom_status = '<span class="badge badge-pill badge-'.(($data->bom_status == "Generated")?"success":"danger").' m-1">'.$data->bom_status.'</span>';

    $action = getActionButton($soBom.$viewBom.$reqButton.$estimationButton);

    return [$action,$data->sr_no,$data->job_number,$data->trans_date,$data->party_name,$data->item_name,$data->qty,$data->bom_status,$data->priority_status,$data->fab_dept_note,$data->pc_dept_note,$data->ass_dept_note,$data->remark];
}

/* Product Option Data */
function getProductOptionData($data){
    $bomParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addProductKitItems', 'title' : 'Create Material BOM [ ".htmlentities($data->item_name)." ]','call_function':'addProductKitItems','button':'close'}";

    $itemProcessParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'viewProductProcess', 'title' : 'Set Product Process [ ".htmlentities($data->item_name)." ]','call_function':'viewProductProcess','button':'close'}";

    $cycleTimeParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'cycleTime', 'title' : 'Set Cycle Time [ ".htmlentities($data->item_name)." ]','call_function':'addCycleTime','button':'both','fnsave':'saveCT'}";

    $dieSetParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'title' : 'Die Set [ ".(!empty($data->item_code) ? $data->item_code." - " : "").$data->item_name." ]','call_function':'addDieSet','button':'close','fnsave':'saveDieSet'}";

    $dieBomParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addDieBom', 'title' : 'Create Die BOM [ ".(!empty($data->item_code) ? $data->item_code." - " : "").$data->item_name." ]','call_function':'addDieBom','button':'close'}";

    $packingParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addPackingStandard', 'title' : 'Add Packing Standard [ ".htmlentities($data->item_name)." ]','call_function':'addPackingStandard','button':'close'}";
	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<a href="'.base_url('productOption/productOptionPrint/'.$data->id).'" type="button" class="btn btn-info" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>
	
				<button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$bomParam .')" datatip="BOM" flow="down" ><i class="fas fa-dolly-flatbed"></i></button>

                <button type="button" class="btn btn-info permission-modify" onclick="addProcess('.$itemProcessParam .')"  datatip="View Process" flow="down"><i class="fa fa-list"></i></button>

                <button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$cycleTimeParam .')" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>

                <button type="button" class="btn btn-info permission-modify" onclick="modalAction('.$dieBomParam .')" datatip="Die BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>

                <button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$packingParam .')" datatip="Packing Standard" flow="down"><i class="fas fa-plus"></i></button>
                
                <!--<button type="button" class="btn btn-twitter permission-modify" onclick="modalAction('.$dieSetParam .')" datatip="Die Set" flow="down"><i class="fa fa-list"></i></button>-->
            </div>';

    return [$data->sr_no,$data->item_code,$data->item_name,$data->bom,$data->process,$data->cycleTime,$btn];
}

/* Outsource Table Data */
function getOutsourceData($data){
    
    $logParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id' : ".$data->prc_id.",'ref_trans_id':".$data->id.",'challan_id':".$data->challan_id.",'processor_id':".$data->party_id.",'process_by':'3','trans_type':".$data->trans_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'Receive Challan', 'js_store_fn' : 'storeSop', 'fnsave' : 'savePRCLog','controller':'sopDesk','button':'close'}";
	
	$btnLbl = (($data->unit_id == 2) ? "Send Material" : "Receive Challan" );
	
    $logBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="'.$btnLbl.'" flow="down" onclick="modalAction('.$logParam.')"><i class=" fas fa-paper-plane"></i></a>';

    $pending_qty = ($data->qty * $data->output_qty) - ($data->ok_qty+$data->rej_qty+$data->without_process_qty);
    $deleteButton = "";
    if($pending_qty > 0 AND $data->unit_id != 2){
        $deleteParam = "{'postData':{'id' : ".$data->challan_id."}}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }

    $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';  $cancelEwb = ''; $syncEWB = '';

    //if(empty($data->eway_bill_no)):
        $syncEwbParam = "{'postData':{'id' : ".$data->out_id.",'party_id' : ".$data->party_id.",'doc_type':'JOBWORK'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Challan No. : ".($data->ch_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1,'button':'close'}";
        $syncEWB = '<a href="javascript:void(0)" class="btn btn-primary" datatip="SYNC E-way Bill" flow="down" onclick="ebillFrom('.$syncEwbParam.');"><i class="mdi mdi-repeat"></i></a>';
    //endif;
    
    if(!empty($data->ewb_status)):
        $ewbPDF = '<a href="'.base_url('ebill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $ewbDetailPDF = '<a href="'.base_url('ebill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fas fa-print"></i></a>';

        if($data->ewb_status == 3):
            $ewbParam = "{'postData':{'id' : ".$data->out_id.",'party_id' : ".$data->party_id.",'doc_type' : 'JOBWORK'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Challan No. : ".($data->ch_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fas fa-truck"></i></a>';
        else:
            $cancelEwbParam = "{'postData':{'id' : ".$data->out_id.",'doc_type' : 'JOBWORK'}, 'modal_id' : 'modal-md', 'form_id' : 'cancelEwb', 'title' : 'Cancel Eway Bill [ Challan No. : ".($data->ch_number)." ]', 'fnedit' : 'loadCancelEwayBillForm', 'fnsave' : 'cancelEwayBill', 'js_store_fn' : 'cancelEwayBill','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel EWB'}";
            $cancelEwb = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Eway Bill" flow="down" onclick="ebillFrom('.$cancelEwbParam.');"><i class="fas fa-times"></i></a>';

            $editButton="";$deleteButton="";
        endif;
    else:
        if(empty($data->eway_bill_no)):
            $ewbParam = "{'postData':{'id' : ".$data->out_id.",'party_id' : ".$data->party_id.",'doc_type' : 'JOBWORK'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Challan No. : ".($data->ch_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        endif;
    endif;

    if($data->trans_status == 1):
        $generateEWB = '';  $cancelEwb = '';
    endif;
    
    $print = '<a href="'.base_url('outsource/outSourcePrint/'.$data->out_id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

    $print2 = '<a href="javascript:void(0)" class="btn btn-primary printChallanDialog" datatip="Print" flow="down" data-id="'.$data->out_id.'" data-req_id="'.$data->id.'" data-fn_name="jobworkOutChallan"><i class="fas fa-print"></i></a>';
	$print = "";

    $action = getActionButton($print.$print2.$ewbPDF.$ewbDetailPDF.$syncEWB.$generateEWB.$cancelEwb.$logBtn.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->ch_date)),$data->ch_number,$data->prc_number,$data->party_name,$data->item_name,$data->process_name,$data->batch_no,floatVal($data->qty),floatVal($data->ok_qty),floatval($data->rej_qty),floatval($data->without_process_qty),floatVal($pending_qty),floatVal($data->price),round(($data->price * $data->qty),2),$data->material_wt,$data->material_price,$data->pre_process_cost,$data->material_value];
}

/* Get Pending Rejection Review Data */
function getPendingReviewData($data){
    $rwBtn=$rejTag ="";
    $title = '[ Pending Decision : '.floatval($data->pending_qty).' ]';
    $okBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-md-modal', 'form_id' : 'okOutWard', 'title' : 'Ok ".$title."','button' : 'both','call_function' : 'convertToOk','fnsave' : 'saveReview'}";
    $rejBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-lg-modal', 'form_id' : 'rejOutWard', 'title' : 'Rejection ".$title." ','button' : 'both','call_function' : 'convertToRej','fnsave' : 'saveReview', 'js_store_fn' : 'customStore'}";
    $rwBtnParam="{'postData':{'id' : " . $data->id . ",'source':'".$data->source."'} ,'modal_id' : 'bs-right-lg-modal', 'form_id' : 'rwOutWard', 'title' : 'Rework ".$title." ','button' : 'both','call_function' : 'convertToRw','fnsave' : 'saveReview', 'js_store_fn' : 'customStore'}";

	$okBtn = '<a  onclick="modalAction('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="mdi mdi-check"></i></a>';
    $rejBtn = '<a onclick="modalAction(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="mdi mdi-close"></i></a>';
    if($data->source == 'MFG'){
        $rwBtn = '<a  onclick="modalAction('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';

        $rejTag = '<a href="' . base_url('sopDesk/printPRCRejLog/' . $data->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
    }
	
	
    $action = getActionButton($okBtn.$rejBtn.$rwBtn.$rejTag);

    if($data->source == 'GRN'){
        return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,floatval($data->qty),floatval($data->review_qty),floatval($data->pending_qty)];
    }elseif($data->source == 'Manual'){
        return [$action,$data->sr_no,formatDate($data->trans_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,$data->location,$data->batch_no,floatval($data->qty),floatval($data->review_qty),floatval($data->pending_qty)]; 
    }else{
        $process_name = ($data->source == 'FIR')?'Final Inspection':$data->process_name;
        return [$action,$data->sr_no,$data->prc_number,'['.$data->item_code.'] '.$data->item_name,formatDate($data->trans_date),$process_name,(!empty($data->processor_name)?$data->processor_name:''),$data->emp_name,$data->rej_found,$data->review_qty,$data->pending_qty];
    }
}

/* Get Rejection Review Data */
function getRejectionReviewData($data){
    
    $deleteParam = "{'postData':{'id' : ".$data->id."},'fndelete':'deleteReview'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    $rejTag = "";
    if($data->decision_type == 1 && $data->source == 'MFG'){
        $rejTag = '<a href="' . base_url('rejectionReview/printRejTag/' . $data->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
    }
    $action = getActionButton($rejTag.$deleteButton);

    if($data->source == 'GRN'){
        return [$action,$data->sr_no,$data->source,$data->trans_number,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,formatDate($data->created_at),$data->decision,floatval($data->qty)];
    }elseif($data->source == 'Manual'){
        return [$action,$data->sr_no,$data->source,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,$data->location,$data->batch_no,formatDate($data->created_at),$data->decision,floatval($data->qty)];
    }else{
        return [$action,$data->sr_no,$data->prc_number,'['.$data->item_code.'] '.$data->item_name,formatDate($data->created_at),$data->decision,$data->process_name,$data->rr_process_name,$data->qty,$data->rr_by_name,$data->rej_mc_code,$data->rej_emp_name,$data->rr_comment];
    }
}

/* Cutting PRC Table Data */
function getCuttingData($data){
    $deleteButton = ""; $editButton=""; $startButton = ""; $logButton = "";$lineInspection = "";$materialBtn ="";$shortBtn=""; $issueMaterialBtn = "";$completeBtn = "";$reopenBtn="";
    if($data->status == 1){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Cutting Job'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPRC', 'title' : 'Update Cutting PRC','call_function':'editCutting','fnsave':'saveCutting'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
        $startParam = "{'postData':{'id' : ".$data->id."},'message' : 'Are you sure you want to start PRC ? once you start you can not edit or delete','fnsave':'startCuttingPRC'}";
        $startButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Start" flow="down" onclick="confirmStore('.$startParam.');"><i class=" fas fa-play"></i></a>';
    
    }else{
        $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addCuttingLog', 'title' : 'Production Log [Job No : ".$data->prc_number." | Batch No : ".$data->batch_no."]','call_function':'addCuttingLog','controller':'cutting','button':'close'}";
        $logButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Log" flow="down" onclick="modalAction('.$logParam.');"><i class=" fas fa-paper-plane
        "></i></a>';

        $reportParam = "{'postData':{'prc_id' : ".$data->id.",'process_id' :'3','prc_type':'2','control_method':'IPR','report_type':'1'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'iirInsp', 'title' : 'Line Inspection','call_function':'addLineInspection','fnsave':'saveLineInspection','controller':'lineInspection'}";
	    $lineInspection = '<a href="javascript:void(0)" type="button" class="btn btn-warning permission-modify" datatip="Line Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';

    }

    if($data->status == 2){
        $shortParam = "{'postData':{'id' : ".$data->id.", 'status' : 5},'message' : 'Are you sure want to Short Close this PRC ?', 'fnsave' : 'changePrcStage'}";
        $shortBtn = ' <a class="btn btn-danger permission-modify " href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortParam.')"><i class="fas mdi mdi-close-circle-outline"></i></a>';

        $completeParam = "{'postData':{'id' : ".$data->id.", 'status' : 3},'message' : 'Are you sure want to Complete this PRC ?', 'fnsave' : 'changePrcStage'}";
        $completeBtn = ' <a class="btn btn-primary permission-modify " href="javascript:void(0)" datatip="Complete" flow="down" onclick="confirmStore('.$completeParam.')"><i class="mdi mdi-check-decagram"></i></a>';
    }
    
    if(in_array($data->status,[1,2])){
        $mtParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->item_id.",'prc_qty':".$data->prc_qty.",'prc_type':'".$data->prc_type."','cut_weight':'".$data->cut_weight."'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'prcMaterial', 'title' : 'Issue Material For Job ".$data->prc_number."', 'fnsave' : 'savePrcMaterial', 'js_store_fn' : 'store','call_function':'requiredMaterial','controller':'cutting'}";
        $materialBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="modalAction('.$mtParam.');"><i class="fas fa-clipboard-check"></i></a>';
    }

    if(in_array($data->status,[3,5])){
        $reopenParam = "{'postData':{'id' : ".$data->id.", 'status' : 2},'message' : 'Are you sure want to Reopen this PRC ?', 'fnsave' : 'changePrcStage'}";
        $reopenBtn = ' <a class="btn btn-info permission-modify " href="javascript:void(0)" datatip="Reopen" flow="down" onclick="confirmStore('.$reopenParam.')"><i class="mdi mdi-restart"></i></a>';
    }
    if(!empty($data->batch_no)){
        $title = 'Material Detail For '.$data->prc_number;
        $issuedParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'prcMaterial', 'title' : '".$title."', 'fnsave' : 'savePrcMaterial', 'js_store_fn' : 'store','call_function':'getMaterialDetail','controller':'cutting','button':'close'}";
        $issueMaterialBtn = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Material Detail" flow="down" onclick="modalAction('.$issuedParam.');"><i class="fas fa-th"></i></a>';
    }
    
    $print = '<a href="'.base_url('cutting/cuttingPrint/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	if(!empty($data->cut_weight)){
		$wtArr = explode('-',$data->cut_weight);
		if(!empty($wtArr[1])){
			$data->cut_weight = $wtArr[1];
		}
	}

	$action = getActionButton($reopenBtn.$logButton.$lineInspection.$startButton.$materialBtn.$issueMaterialBtn.$completeBtn.$shortBtn.$print.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->so_number,formatDate($data->so_date),$data->item_name,floatval($data->prc_qty),floatval($data->production_qty),$data->batch_no,floatval($data->cutting_length),floatval($data->cutting_dia),floatval($data->cut_weight),$data->cutting_type,$data->job_instruction];
}

function getSopData($data){
    $materialBtn = $startButton = $editButton = $deleteButton = $holdBtn = $shortBtn = $restartBtn = $updateQty="";
	$prc_number = '<a href="'.base_url("sopDesk/prcDetail/".$data->id).'">'.$data->prc_number.'</a>';

    if($data->status == 1 || $data->status == 2){
        $mtParam = "{'postData':{'id' : ".$data->id.",'prc_qty' : ".$data->prc_qty.",'item_id':".$data->item_id.",'prc_type':".$data->prc_type."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'prcMaterial', 'title' : 'Material Required For : ".$data->prc_number."', 'fnsave' : 'savePrcMaterial','call_function':'requiredMaterial'}";
        $materialBtn = ' <a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Required Material" flow="down" onclick="modalAction('.$mtParam.')"><i class="far fa-paper-plane"></i></a>';
    }
    if($data->status == 1 ){
        $startTitle = 'Start PRC : '.$data->prc_number;
        $startParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->item_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPrc', 'title' : '".$startTitle."', 'fnsave' : 'startPrc', 'js_store_fn' : 'confirmStore','call_function':'setPrcProcesses'}";
        $startButton = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Start" flow="down" onclick="modalAction('.$startParam.')"><i class="fas fa-play-circle"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPrc', 'title' : 'Update PRC', 'fnsave' : 'savePRC'}";
        $editButton= ' <a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.')"><i class="far fa-edit"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'PRC'}";
        $deleteButton = ' <a class="btn btn-danger permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="trash('.$deleteParam.')"><i class="mdi mdi-trash-can-outline"></i></a>';
    }elseif($data->status == 2){

        /*** IF PRC IS IN PROGRSS THEN PROCESS BUTTON */
        $startTitle = 'PRC Process: '.$data->prc_number;
        $startParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->item_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPrc', 'title' : '".$startTitle."', 'fnsave' : 'startPrc', 'js_store_fn' : 'confirmStore','call_function':'setPrcProcesses'}";
        $startButton = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="PRC Process" flow="down" onclick="modalAction('.$startParam.')"><i class="fas fa-play-circle"></i></a>';
        $updateQtyParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'updatePrcQty', 'title' : 'Update PRC Qty [".$data->prc_number."] ', 'call_function' : 'updatePrcQty', 'button' : 'close'}";
        $updateQty= '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Update PRC Qty." flow="down" onclick="modalAction(' . $updateQtyParam . ');"><i class="far fa-plus-square"></i> </a>';
        
        $holdParam = "{'postData':{'id' : ".$data->id.", 'status' : 4},'message' : 'Are you sure want to Hold this PRC ?', 'fnsave' : 'changePrcStage'}";
        $holdBtn= ' <a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" onclick="confirmStore('.$holdParam.')"><i class="far fa-pause-circle"></i></a>';
        
        $shortParam = "{'postData':{'id' : ".$data->id.", 'status' : 5},'message' : 'Are you sure want to Short Close this PRC ?', 'fnsave' : 'changePrcStage'}";
        $shortBtn = ' <a class="btn btn-danger permission-modify " href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortParam.')"><i class="fas mdi mdi-close-circle-outline"></i></a>';
    }
    elseif($data->status == 4 || $data->status == 5){
        $restartParam = "{'postData':{'id' : ".$data->id.", 'status' : 2},'message' : 'Are you sure want to Restart this PRC ?', 'fnsave' : 'changePrcStage'}";
        $restartBtn = ' <a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" onclick="confirmStore('.$restartParam.')"><i class="mdi mdi-restart"></i></a>';
    }
	
	$poDetail = "";
	if(!empty($data->doc_no)){$poDetail = $data->doc_no.'<br><small>('.$data->doc_date.')</small>';}
	
    $action = getActionButton($materialBtn.$startButton.$holdBtn.$shortBtn.$restartBtn.$updateQty.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->mfg_type,$prc_number,formatDate($data->prc_date),$data->party_name,$data->so_number,formatDate($data->so_date),$poDetail,$data->item_code.' '.$data->item_name,$data->batch_no,floatval($data->prc_qty),$data->target_date,$data->remark];
}

function getProductionShortageData($data){
    $addParam = "{'postData':{'item_id' : ".$data->item_id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addPRC', 'form_id' : 'addPRC', 'title' : 'New PRC', 'fnsave' : 'savePRC'}";
    $prcBtn= '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Add PRC" flow="down" onclick="modalAction(' . $addParam . ');"><i class="far fa-plus-square"></i> </a>';
    
	$sort_qty = ($data->total_qty - ($data->total_dispatch_qty+$data->wip_qty+$data->prd_finish_Stock+$data->rtd_Stock));
	$sortage_qty = (($sort_qty>0)?$sort_qty:0);
	
	
	$action = getActionButton($prcBtn);
    return [$action,$data->sr_no,$data->item_code.' '.$data->item_name,$data->party_name,$data->so_number,$data->qty,floatval($data->total_qty),floatval($data->total_dispatch_qty),floatval($data->wip_qty),floatval($data->prd_finish_Stock),floatval($data->rtd_Stock),floatval($sortage_qty)];
}

function getPrcLogData($data){
    $in_qty = (!empty($data->accepted_qty)?$data->accepted_qty:0);
    $ok_qty = !empty($data->ok_qty)?$data->ok_qty:0;
    $rej_found_qty = !empty($data->rej_found)?$data->rej_found:0;
    $rej_qty = !empty($data->rej_qty)?$data->rej_qty:0;
    $rw_qty = !empty($data->rw_qty)?$data->rw_qty:0;
    $pendingReview = $rej_found_qty - $data->review_qty;
    $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
    $movement_qty =!empty($data->movement_qty)?$data->movement_qty:0;
    $short_qty =!empty($data->short_qty)?$data->short_qty:0;
    $pending_movement = ($ok_qty - $movement_qty);
    $pending_accept =$data->inward_qty - $data->accepted_qty;

    $logBtn = "";$movementBtn="";$chReqBtn="";$receiveBtn="";$firButton = "";
    if($data->process_id == 2){
        // $logParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'PRC LOG', 'fnsave' : 'savePRCLog','button':'close'}";
        // $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-success permission-modify" datatip="Add Log" flow="down"><i class="fas fa-clipboard-list"></i></a>';

        $reportParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'firInsp', 'title' : 'Final Inspection','call_function':'addFinalInspection','fnsave':'savePrcLog', 'js_store_fn' : 'customStore'}";
	    $firButton = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Final Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';
    }else{
        $logParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'PRC LOG', 'fnsave' : 'savePRCLog','button':'close'}";
        $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-success permission-modify" datatip="Add Log" flow="down"><i class="fas fa-clipboard-list"></i></a>';
    
        $title = '';
        $chReqParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-md-modal', 'call_function':'challanRequest', 'form_id' : 'addChallanRequest', 'title' : 'Challan Request ".$title ."', 'fnsave' : 'saveAcceptedQty','button':'close'}";
        $chReqBtn = '<a href="javascript:void(0)" class="btn btn-warning permission-modify" datatip="Challan Request" flow="down" onclick="modalAction('.$chReqParam .')"><i class="fab fa-telegram-plane"></i></a>';
    }
    
    $movementParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'move_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement', 'fnsave' : 'savePRCMovement','button':'close'}";
    $movementBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" datatip="Movement" flow="down" onclick="modalAction('.$movementParam.')"><i class="fa fa-step-forward"></i></a>';
    $acceptBtn="";
    if($pending_accept > 0 || $in_qty > 0){
        $title = '[Pending Qty : '.floatval($pending_accept).']';
        $acceptParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'trans_type':".$data->move_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-md-modal', 'call_function':'prcAccept', 'form_id' : 'addPrcAccept', 'title' : 'Accept For Production ".$title."', 'fnsave' : 'saveAcceptedQty','button':'close'}";
        $acceptBtn = '<a href="javascript:void(0)" class="btn btn-dark permission-modify" datatip="Accept" flow="down" onclick="modalAction('.$acceptParam .')"><i class="far fa-check-circle"></i></a>';
    }

    
    $action = getActionButton($acceptBtn.$firButton.$logBtn.$chReqBtn.$movementBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->item_code.' '.$data->item_name,
    (!empty($data->from_process_name)?$data->from_process_name:'Initial Stage'),
    floatval($pending_accept),
    floatval($in_qty),
    floatval($ok_qty),
    floatval($rej_found_qty),
    floatval($rej_qty),
    floatval($pending_production),
    floatval($pending_movement)];
}

function getSemiFinishedLogData($data){
    $in_qty = (!empty($data->in_qty)?$data->in_qty:0);
    $pending_accept =!empty($data->pending_accept)?$data->pending_accept:0;

    $movementParam = "{'postData':{'process_id' : 1,'prc_id':".$data->prc_id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'semiFinishMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement', 'js_store_fn' : 'storeSop', 'fnsave' : 'savePRCMovement','button':'close'}";
    $movementBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" datatip="Movement" flow="down" onclick="modalAction('.$movementParam.')"><i class="fa fa-step-forward"></i></a>';
    $action = getActionButton($movementBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->item_code.' '.$data->item_name,floatval($pending_accept),floatval($in_qty),""];
}

function getMfgStoreData($data){
    $editButton = $deleteButton = $issueButton = "";$demand = '';
    if($data->issue_qty <= 0 && $data->current_process == $data->req_from){
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editRequest', 'title' : 'Update Request', 'fnsave' : 'saveMfgRequest','call_function':'editMfgRequest'}";
        $editButton= ' <a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.')"><i class="far fa-edit"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Request','fndelete' : 'deleteMfgRequest'}";
        $deleteButton = ' <a class="btn btn-danger btn-edit" href="javascript:void(0)" datatip="Delete" flow="down" onclick="trash('.$deleteParam.')"><i class="mdi mdi-trash-can-outline"></i></a>';
        
    }
    if($data->current_process == $data->req_from){
        $demand = $data->req_to_process;
    }
    if($data->current_process == $data->req_to){
        $issueparam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'issueItem', 'title' : 'Issue Item', 'fnsave' : 'saveIssuedItem','call_function':'issueRequestedItem'}";
        $issueButton= ' <a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Issue" flow="down" onclick="modalAction('.$issueparam.')"><i class="far fa-paper-plane"></i></a>';
        $demand = $data->req_from_process;
    }
    $action = getActionButton($issueButton.$editButton.$deleteButton);
    $pending_qty = $data->qty - $data->issue_qty;
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$demand,$data->item_code.' '.$data->item_name,floatval($data->qty),floatval($data->issue_qty),$pending_qty];
}

function getMfgStoreStockData($data){
    $ok_qty = !empty($data->ok_qty)?$data->ok_qty:0;
    $movement_qty =!empty($data->movement_qty)?$data->movement_qty:0;
    $pending_movement = ($ok_qty - $movement_qty);

    $movementBtn="";
    
    $movementParam = "{'postData':{'process_id' : ".$data->process_id.",'prc_id':".$data->prc_id.",'move_type':".$data->trans_type.",'process_from':".$data->process_from."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement', 'fnsave' : 'savePRCMovement','button':'close'}";
    $movementBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" datatip="Movement" flow="down" onclick="modalAction('.$movementParam.')"><i class="fa fa-step-forward"></i></a>';
   
    
    $action = getActionButton($movementBtn);
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->item_code.' '.$data->item_name,(!empty($data->from_process_name)?$data->from_process_name:'Initial Stage'),(($data->trans_type == 1)?'Regular':'Rework'), floatval($pending_movement)];
}

/* Die Master Table Data */
function getDieMasterData($data) {
    $deleteButton='';
    if($data->status == 0){
        $status = "Active";
    }
    $action = getActionButton($deleteButton);

    return [ $action,$data->sr_no,$data->item_name,$data->category_name,$data->die_no,$data->capacity,$status];
}

/* Part List Table Data */
function getPartListData($data) {
    $rejectBtn = $recutBtn = $popReportBtn = $approveBtn = '';

    $deleteParam = "{'postData':{'id' : ".$data->id."}, 'message' : 'Component', 'fndelete' : 'deleteComponent'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    if(empty($data->status) && $data->is_inspection == 1){
        $popReportParam = "{'postData':{'id' : ".$data->id.", 'type' : 'Component', 'item_id' : ".$data->fg_id.", 'category_id' : ".$data->category_id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addPopReport', 'call_function' : 'addPopReport', 'fnsave' : 'savePopReport', 'title' : 'POP Report', 'controller' : 'dieProduction'}";
        $popReportBtn = '<a class="btn btn-dribbble permission-modify" href="javascript:void(0)" datatip="POP Report" flow="down" onclick="modalAction('.$popReportParam.');"><i class="fas fa-file-alt"></i></a>';
    }
    elseif($data->status == 5 || (empty($data->status) && $data->is_inspection == 0)){
        $approveParam = "{'postData':{'id' : ".$data->id.", 'status' : '1', 'type' : 'Component', 'item_id' : ".$data->fg_id.", 'category_id' : ".$data->category_id."}, 'modal_id' : 'bs_approval_modal', 'form_id' : 'approveProduction', 'call_function' : 'approveProduction', 'fnsave' : 'changeStatus', 'title' : 'Approve', 'controller' : 'dieProduction'}";
        $approveBtn = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalApproveAction('.$approveParam.');"><i class="fas fa-paper-plane" ></i></a>';
    }
    elseif($data->status == 1){
        $deleteButton = '';
        $rejectParam = "{'postData' : {'id' : '".$data->id."'}, 'modal_id' : 'modal-md', 'form_id' : 'rejectPart', 'title' : 'Reject Part','call_function':'rejectPart', 'fnsave' : 'changePartStatus'}";
        $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="modalAction('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';   
        
        $recutParam = "{'postData':{'id' : ".$data->id.", 'fg_id' : '".$data->fg_id."', 'category_id' : '".$data->category_id."', 'status' : 3, 'msg' : 'Recut'},'fnsave':'recutDie','message':'Are you sure want to Recut this Part?'}";
        $recutBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Recut" flow="down" onclick="confirmStore('.$recutParam.');"><i class="fas fa-cogs"></i></a>'; 
    }
	
	$historyParam = "{'postData':{'die_id' : ".$data->id.", 'die_job_id' : '".$data->die_job_id."'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'viewDieHistory', 'call_function' : 'viewDieHistory', 'title' : '".$data->die_code.' ('.$data->category_name.")', 'button' : 'close'}";
	$historyBtn = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="View Die History" flow="down" onclick="modalAction('.$historyParam.');"><i class="fas fa-eye"></i></a>';
    
    $action = getActionButton($approveBtn.$popReportBtn.$historyBtn.$recutBtn.$rejectBtn.$deleteButton);
    return [$action,$data->sr_no,$data->die_code,$data->category_name,$data->fg_item_code.' - '.$data->fg_item_name,((!empty($data->ch_number))?$data->ch_number:'')];
}

/* Die Recut Table Data */
function getDieRecutData($data) {
    $inProgressBtn = $completeBtn = '';
    if($data->status == 0){
        $inProgressParam = "{'postData':{'id' : ".$data->id.", 'status' : 1, 'msg' : 'In Progress'},'fnsave':'changeRecutStatus','message':'Are you sure want to Start this Die Recut?'}";
        $inProgressBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Start" flow="down" onclick="confirmStore('.$inProgressParam.');"><i class="mdi mdi-play"></i></a>';   
    }elseif($data->status == 1){
        $completeParam = "{'postData':{'id' : ".$data->id." , 'die_id' : '".$data->die_id."', 'fg_id' : '".$data->fg_id."', 'item_code' : '".$data->item_code."', 'capacity' : '".$data->capacity."', 'category_id' : '".$data->category_id."', 'set_no' : '".$data->set_no."'}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'completeRecut', 'call_function' : 'completeRecut', 'title' : 'Complete Recut','fnsave':'changeRecutStatus'}";
        $completeBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Complete" flow="down" onclick="modalAction('.$completeParam.');"><i class="mdi mdi-check"></i></a>';
	}

    $action = getActionButton($completeBtn.$inProgressBtn);

    return [$action,$data->sr_no,$data->item_code,$data->fg_item_name,$data->status_label];
}

/* Die Production Table Data */
function getDieOutsourceData($data) {
    $selectBox = '<input type="checkbox" name="dp_id[]" id="dp_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkChallan" value="'.$data->id.'"><label for="dp_id_'.$data->sr_no.'"></label>';
    return [ $selectBox,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->category_name,(($data->fg_item_code) ? "[".$data->fg_item_code."] " : "").$data->fg_item_name,$data->remark]; 
}

/* Die Production Table Data */
function getDieOutsourceChallanData($data) {
    $deleteButton = "";$logBtn="";$detailBtn = "";
    if($data->status == 1){
        $logParam = "{'postData':{'id' : ".$data->dp_id.",'challan_id':".$data->id.",'party_id':".$data->party_id.",'process_by':'2'},'modal_id' : 'bs-right-lg-modal', 'call_function':'addDieLog', 'form_id' : 'addDieLog', 'title' : 'Receive Challan', 'fnsave' : 'saveDieLog','controller':'dieProduction'}";
        $logBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Receive Challan" flow="down" onclick="modalAction('.$logParam.')"><i class="fas fa-paper-plane"></i></a>';
    
        $deleteParam = "{'postData':{'ch_number' : '".$data->ch_number."'}}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }else{
        $logParam = "{'postData':{'id' : ".$data->dp_id."},'modal_id' : 'master-modal-md', 'call_function':'logDetail', 'form_id' : 'logDetail', 'title' : 'Log Detail', 'fnsave' : 'deleteDieLog','controller':'dieProduction','savebtn_text':'  Delete','js_store_fn':'confirmStore','message':'Are you sure you want to delete this log'}";
        $detailBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Log Detail" flow="down" onclick="modalAction('.$logParam.')"><i class="fas fa-eye"></i></a>';
    }
    $print = '<a href="'.base_url('dieOutsource/dieOutSourcePrint/'.$data->ch_number).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($print.$detailBtn.$logBtn.$deleteButton);
    return [ $action,$data->sr_no,$data->ch_number,formatDate($data->ch_date ),$data->trans_number,$data->category_name,(($data->fg_item_code) ? "[".$data->fg_item_code."] " : "").$data->fg_item_name]; 
}

/* Die Master Table Data */
function getDieScrapData($data) {
   
    $itemCode =  $data->category_code.'-'.$data->fg_item_code.$data->set_no.$data->sr_no.((!empty($data->recut_no)?'/'.lpad($data->recut_no,2):'')); //Generate Die Code

    $scrapQty = ($data->material_weight - $data->weight) ;
    $scrapButton = "";
    if($scrapQty > 0){
        $scrapParam = "{'postData':{'id' : ".$data->die_job_id."},'message' : 'Are you sure you want to Generate Scrap','fnsave':'generateScrap'}";
        $scrapButton = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Generate Scrap" flow="down" onclick="confirmStore('.$scrapParam.');"><i class=" fas fa-plus"></i></a>';
    }
    
    $action = getActionButton($scrapButton);
    return [ $action,$data->sr_no,$data->fg_item_name,$data->category_name,$itemCode,$data->weight,$data->material_weight,round($scrapQty,2)];
}

/* Die Production Table Data */
function getDieProductionData($data) { 
    $status = $deleteButton = $inProgress = $completeBtn = $approveBtn = $issueBtn = $logBtn = $challanBtn = $popReportBtn = $rejBtn = $recutBtn = $detailBtn = $mtValBtn = $logDetailBtn = "";

    if($data->status == 1){
        $status = '<span class="badge bg-pink fw-semibold font-12 v-super">Pending</span>';

        $inProgress = '<a class="btn btn-warning btn-start changeStatus permission-modify" href="javascript:void(0)" datatip="In Progress"  flow="down" data-msg="In Progress" data-val="2" data-id="'.$data->id.'"><i class="mdi mdi-play" ></i></a>';
        
	} else if($data->status == 2){
        $status = '<span class="badge bg-info fw-semibold font-12 v-super">In Progress</span>';

        if($data->trans_type == 1 && $data->issue_qty == 0){           
            $issueParam = "{'postData':{'id' : ".$data->id.",'item_id':".$data->fg_item_id.",'category_id':".$data->item_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'materialIssue', 'title' : 'Material Request [ ".$data->fg_item_code." ]', 'fnsave' : 'saveMaterialIssue','call_function':'materialIssue'}";
            $issueBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Material Request" flow="down" onclick="modalAction('.$issueParam.');"><i class="fas fa-clipboard-check"></i></a>';
        }
        else if(($data->issue_qty >= $data->qty) || $data->trans_type == 2){
                $status = '<span class="badge bg-primary fw-semibold font-12 v-super">Material Issued</span>';

                $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addDieLog', 'form_id' : 'addDieLog', 'title' : 'Add Die Log', 'fnsave' : 'saveDieLog'}";
                $logBtn = '<a href="javascript:void(0)" onclick="modalAction('.$logParam.')" class="btn btn-primary permission-modify" datatip="Add Log" flow="down"><i class="fas fa-stop-circle"></i></a>';
                
                if(empty($data->log_id)){
                    $challanBtn = '<a class="btn btn-dark changeStatus permission-modify" href="javascript:void(0)" datatip="Challan Request"  flow="down" data-msg="Send Challan Request" data-val="3" data-id="'.$data->id.'"><i class="fas fa-paper-plane"></i></a>';
                }else{
                    $inProgress = '<a class="btn btn-warning btn-start changeStatus permission-modify" href="javascript:void(0)" datatip="Complete Machining"  flow="down" data-msg="Complete Machining" data-val="5" data-id="'.$data->id.'"><i class="fas fa-check" ></i></a>';
                }
                
            }
    }
    else if($data->status == 5){  
        $status = '<span class="badge bg-success fw-semibold font-12 v-super">M/C Done</span>';

        $popReportParam = "{'postData':{'id' : ".$data->id.", 'item_id' : ".$data->fg_item_id.", 'category_id' : ".$data->item_id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addPopReport', 'call_function' : 'addPopReport', 'fnsave' : 'savePopReport', 'title' : 'POP Report'}";
        $popReportBtn = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="POP Report" flow="down" onclick="modalAction('.$popReportParam.');"><i class="fas fa-file-alt"></i></a>';
    }elseif($data->status == 6){
        $status = '<span class="badge bg-purple fw-semibold font-12 v-super">POP Done</span>';

		$approveParam = "{'postData':{'id' : " . $data->id . ",'status':'9', 'item_id' : ".$data->fg_item_id.", 'category_id' : ".$data->item_id."}, 'modal_id' : 'bs_approval_modal', 'form_id' : 'approveProduction', 'call_function' : 'approveProduction', 'fnsave' : 'changeStatus', 'title' : 'Approve'}";
        $approveBtn = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalApproveAction('.$approveParam.');"><i class="fas fa-paper-plane" ></i></a>';
    }
    elseif($data->status == 7){
        $status = '<span class="badge bg-danger fw-semibold font-12 v-super">Recut</span>';
    }
    elseif($data->status == 8){
        $status = '<span class="badge bg-dark fw-semibold font-12 v-super">Reject</span>';
    }
    elseif($data->status == 9){
        $status = '<span class="badge bg-success fw-semibold font-12 v-super">Approved</span>';
    }
    if($data->status >= 2){
        if($data->status >= 5 && $data->process_by == 2){
            $button = "close";
            if($data->status == 5){
                $button = "";
            }
            $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'master-modal-md', 'call_function':'logDetail', 'form_id' : 'logDetail', 'title' : 'Log Detail', 'fnsave' : 'deleteDieLog','controller':'dieProduction','savebtn_text':'  Delete','js_store_fn':'confirmStore','message':'Are you sure you want to delete this log','button':'".$button."'}";
            $detailBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Log Detail" flow="down" onclick="modalAction('.$logParam.')"><i class="fas fa-eye"></i></a>';
        }
        else{
            $logParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-xl', 'call_function':'logTansDetail', 'form_id' : 'logTansDetail', 'title' : 'Log Detail','button':'close'}";
            $logDetailBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Log Detail" flow="down" onclick="modalAction('.$logParam.')"><i class="fas fa-eye"></i></a>';
        }
    }
    $print = "";
    if($data->status >= 6){
        $print = '<a href="'.base_url('dieProduction/printPop/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    }
	
	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Die Production'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';  

    $action = getActionButton($mtValBtn.$print.$approveBtn.$rejBtn.$recutBtn.$logDetailBtn.$detailBtn.$popReportBtn.$challanBtn.$logBtn.$issueBtn.$inProgress.$completeBtn.$deleteButton);
    $type = "";
    if($data->trans_type == 1){
        $type= '<a href="javascript:void(0)" class="badge bg-primary font-12">'.$data->transType.'</a>';
    }else{
        $type= '<a href="javascript:void(0)" class="badge bg-danger font-12">'.$data->transType.'</a><br>'.$data->die_code;
    }
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$type,$data->category_name,(($data->fg_item_code) ? "[".$data->fg_item_code."] " : "").$data->fg_item_name,$data->remark,$status]; 
}

/* Die Challan Data */
function getDieChallanData($data){
    $returnBtn=''; $edit=''; $delete='';
    
    if(empty($data->receive_by)){
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->challan_id."},'message' : 'Challan'}";
        $delete = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $rtnParam = "{'postData':{'id' : ".$data->id.",'challan_type' : ".$data->challan_type."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'returnChallan', 'title' : 'Return Challan For [".$data->item_code."]', 'call_function' : 'returnChallan', 'fnsave' : 'saveReturnDie'}";
        $returnBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" onclick="modalAction('.$rtnParam.');" datatip="Return" flow="down"><i class="mdi mdi-reply"></i></a>';
    }
        
    $printBtn = '<a class="btn btn-primary btn-edit" href="'.base_url('dieChallan/printChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
    
    $action = getActionButton($printBtn.$returnBtn.$edit.$delete);
    //return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->issue_to,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,$data->die_set_no,$data->prc_number,$data->item_remark];
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->issue_to,$data->die_code,$data->die_set_no,$data->prc_number,$data->item_remark];
}

/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $approveBtn = $editButton = $deleteButton = $closeBtn = $revisionButton = '';
    if($data->trans_status == 1){
		$approveParam = "{'postData':{'id' : ".$data->id.", 'msg' : 'Approved'},'fnsave':'approveOrder','message':'Are you sure want to Approve this Order?'}";
		$approveBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';
		
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('jobworkOrder/edit/'.$data->jwo_id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

		$deleteParam = "{'postData':{'id' : ".$data->id.",'jwo_id' : ".$data->jwo_id."},'message' : 'Jobwork Order'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        
    }
	if($data->trans_status == 3){
		$closeParam = "{'postData':{'id' : ".$data->id.", 'msg' : 'Close'},'fnsave':'shortCloseOrder','message':'Are you sure want to Close this Order?'}";
		$closeBtn = '<a class="btn btn-danger permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$closeParam.');"><i class="mdi mdi-close-circle-outline"></i></a>';
		
		$revisionButton = '<a class="btn btn-info btn-edit permission-modify" href="'.base_url('jobworkOrder/createRevision/'.$data->jwo_id).'" datatip="Revision" flow="down"><i class="fas fa-sync-alt"></i></a>';
	}
	
	$printBtn = '<a href="'.base_url('jobworkOrder/printJobwork/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($printBtn.$revisionButton.$closeBtn.$approveBtn.$editButton.$deleteButton);  
    return [$action,$data->sr_no,formatDate($data->order_date),$data->trans_number,$data->party_name,$data->item_code.' '.$data->item_name,$data->process_name,$data->rate_per_unit,sprintf('%0.2f',$data->rate),$data->remark];
}

/*Vendor Challan Table Data */
function getjobWorkBillData($data){
    $selectBox = '<input type="checkbox" name="log_id[]" id="log_id_'.$data->log_ids.'" class="filled-in chk-col-success BulkRequest" value="'.$data->log_ids.'"><label for="log_id_'.$data->log_ids.'"></label>';   

    $billParam = "{'postData':{'log_ids':'".$data->log_ids."'},'modal_id' : 'modal-md', 'form_id' : 'billForm', 'title' : 'Vendor Challan','call_function':'jobWorkBill','fnsave':'saveJobWorkBill','js_store_fn':'customStore'}";
    $billBtn = '<a href="javascript:void(0)" type="button" class="btn btn-success permission-modify" datatip="Vendor Challan" flow="down" onclick="modalAction('.$billParam.');"><i class="fa fa-file-alt"></i></a>';

    $action = getActionButton($billBtn);  
    return[$action,$data->sr_no,$selectBox,formatDate($data->trans_date),$data->in_challan_no,$data->challan_no,$data->party_name];
}

/* RM Challan Table Data */ 
function getRmChallanData($data){

    $print = '<a href="javascript:void(0)" class="btn btn-primary printChallanDialog" datatip="Print" flow="down" data-id="'.$data->out_id.'" data-req_id="'.$data->id.'" data-fn_name="rmChallanPrint"><i class="fas fa-print"></i></a>';

    $action = getActionButton($print);
    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->ch_date)),$data->ch_number,$data->grn_number,$data->party_name,$data->rm_item,floatVal($data->qty),floatVal($data->price),round(($data->material_value),2)];
}

?>
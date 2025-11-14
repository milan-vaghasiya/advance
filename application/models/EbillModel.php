<?php
class EbillModel extends MasterModel{
    private $eBillLog = "e_bill_log";
    private $ewayBillMaster = "eway_bill_master";

    
    /* Generate Eway Bill JSON DATA */
    public function ewbJsonSingle($ewbData){
		$ref_id = $ewbData['ref_id'];
        $postData=array();$billData=array();$itemList=array();

        if($ewbData['document_type'] == "INV"):
			$invData = $this->salesInvoice->getSalesInvoice(['id'=>$ref_id,'itemList'=>1]);
		elseif($ewbData['document_type'] == "CRN"):
			$invData = $this->creditNote->getCreditNote(['id'=>$ref_id,'itemList'=>1]);
		elseif($ewbData['document_type'] == "DBN"):
			$invData = $this->debitNote->getDebitNote(['id'=>$ref_id,'itemList'=>1]);
        elseif($ewbData['document_type'] == "JOBWORK"):
            $invData = $this->outsource->getOutSourceData(['id'=>$ref_id]);
            $invData->itemList = $this->sop->getChallanRequestData(['challan_id'=>$ref_id]);

            $invData->trans_number = $invData->ch_number;
            $invData->trans_date = $invData->ch_date;
            $invData->gst_type = 3;

            $totalAmount = 0;
            foreach($invData->itemList as &$row):
                //$beforWt = ((empty($row->process_from))?$material_wt:((!empty($row->prev_weight) && $row->prev_weight > 0)?$row->prev_weight:0)) ;
                //$kgs = $beforWt * $row->qty;

                //$row->qty = ((!empty($row->uom) && $row->uom == 'KGS' && $kgs > 0)) ? $kgs : $row->qty;

                $row->unit_name = $row->uom;
                $row->taxable_amount = $row->amount = round($row->material_value,2);
                $row->disc_amount = $row->gst_amount = $row->sgst_per = $row->cgst_per = $row->igst_per = $row->exp_taxable_amount = $row->exp_gst_amount = 0;
                $totalAmount += $row->amount;
            endforeach;

            $invData->round_off_amount = $invData->gst_amount = $invData->cgst_amount = $invData->sgst_amount = $invData->igst_amount = 0;
            $invData->taxable_amount = $invData->net_amount = $totalAmount;
		endif;
        $invData = $this->calculateInvoiceValue($invData);

        $orgData = $this->getCompanyInfo();
        $cityDataFrom = $this->party->getState(['id'=>$ewbData['from_state']]);
        $cityDataTo = $this->party->getState(['id'=>$ewbData['ship_state']]);

        if(!empty($ewbData['trans_distance'])):
            $this->edit('party_master',['id'=>$invData->party_id],['distance'=>$ewbData['trans_distance']]);
        endif;

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_phone,
            'contact_no' => $orgData->company_phone,
            'country_name' => (!empty($ewbData['from_city']))?$cityDataFrom->country_name:$orgData->company_country_name,
            'state_name' => (!empty($ewbData['from_city']))?$cityDataFrom->state_name:$orgData->company_state_name,
            'city_name' => (!empty($data['from_city']))?$data['from_city']:$orgData->company_city_name,
            'address' => (!empty($ewbData['from_address']))?$ewbData['from_address']:$orgData->company_address,
            'pincode' => (!empty($ewbData['from_pincode']))?$ewbData['from_pincode']:$orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];

        
        $partyData = $this->party->getParty(['id'=>$invData->party_id]);    
        $shipToData = [];
        if($ewbData['document_type'] == "INV" && !empty($invData->ship_to)):
            $shipToData = $this->party->getParty(['id'=>$invData->ship_to]);
            $ewbData['ship_pincode'] = (!empty($shipToData->party_pincode))?$shipToData->party_pincode:$ewbData['ship_pincode'];
        endif;
        $postData['partyInfo'] = [
            'name' => (!empty($partyData->party_name))?$partyData->party_name:$invData->party_name,
            'gst_no' => (!empty($partyData->gstin))?$partyData->gstin:"URP",
            'pan_no' => (!empty($partyData->pan_no))?$partyData->pan_no:"",            
            'email' => (!empty($partyData->party_email))?$partyData->party_email:"",
            'contact_email' => (!empty($partyData->party_email))?$partyData->party_email:"",
            'phone_no' => (!empty($partyData->party_mobile))?$partyData->party_mobile:"",
            'contact_no' => (!empty($partyData->party_mobile))?$partyData->party_mobile:"",

            'billing_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($data['ship_city']))?$data['ship_city']:$partyData->city_name,
            'billing_state_code' => (!empty($cityDataTo->state_code))?$cityDataTo->state_code:$invData->party_state_code,

            'ship_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($data['ship_city']))?$data['ship_city']:$partyData->city_name,
            'ship_state_code' => (!empty($cityDataTo->state_code))?$cityDataTo->state_code:$invData->party_state_code,
        ];

        $mainHsnCode = '';
        foreach($invData->itemList as $row):            
            $itemList[]= [
                "productName"=> $row->item_name,
                "productDesc"=> "", 
                "hsnCode"=> (!empty($row->hsn_code))?intVal($row->hsn_code):"", 
                "quantity"=> round(floatVal($row->qty),3),
                "qtyUnit"=> $row->unit_name, 
                "taxableAmount"=> round(floatVal($row->amount),2),
                "sgstRate"=> ($invData->gst_type == 1)?round(floatVal($row->sgst_per),2):0,
                "cgstRate"=> ($invData->gst_type == 1)?round(floatVal($row->cgst_per),2):0,
                "igstRate"=> ($invData->gst_type == 2)?round(floatVal($row->igst_per),2):0,
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $mainHsnCode = (!empty($row->hsn_code))?intVal($row->hsn_code):"";
        endforeach;

        $ewbData['from_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['from_address']);
        $orgAdd1 = substr($ewbData['from_address'],0,100);
        $orgAdd2 = (strlen($ewbData['from_address']) > 100)?substr($ewbData['from_address'],100,200):"";

        $ewbData['ship_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['ship_address']);
        $toAddr1 = substr($ewbData['ship_address'],0,100);
        $toAddr2 = (strlen($ewbData['ship_address']) > 100)?substr($ewbData['ship_address'],100,200):"";
                    
        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = ($ewbData['sub_supply_desc']??'');
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = $invData->trans_number;
        $billData["docDate"] = date("d/m/Y",strtotime($invData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $orgAdd1;
        $billData["fromAddr2"] = $orgAdd2;
        $billData["fromPlace"] = $ewbData['from_city'];
        $billData["fromPincode"] = (int) $ewbData['from_pincode'];
        $billData["fromStateCode"] = (int) $orgData->company_state_code;
        $billData["actFromStateCode"] = (int) $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = $toAddr1;
        $billData["toAddr2"] = $toAddr2;
        $billData["toPlace"] = $ewbData['ship_city'];
        $billData["toPincode"] = (int) $ewbData['ship_pincode']; 
        $billData["toStateCode"] = (int) $cityDataTo->state_code;
        $billData["actToStateCode"] = (int) $cityDataTo->state_code;
        $billData['transactionType'] = (int) $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = (!empty($shipToData->gstin))?$shipToData->gstin:"";
        $billData['shipToTradeName'] = (!empty($shipToData->party_name))?$shipToData->party_name:"";
        $billData["otherValue"] = round(floatVal(round(($invData->net_amount + ($invData->round_off_amount * -1)) - ($invData->taxable_amount + $invData->gst_amount),2)),2);
        $billData["totalValue"] = round(floatVal($invData->taxable_amount),2);
        $billData["cgstValue"] = round(floatVal($invData->cgst_amount),2);
        $billData["sgstValue"] = round(floatVal($invData->sgst_amount),2);
        $billData["igstValue"] = round(floatVal($invData->igst_amount),2);
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = round(floatVal($invData->net_amount),2);
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = (!empty($ewbData['transport_doc_date']))?date("d/m/Y",strtotime($ewbData['transport_doc_date'])):"";
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        $billData['mainHsnCode'] = $mainHsnCode;
        $billData['itemList']=$itemList;
        
		$postData['ewbData'] = $billData;
        
		return $postData;
    }

    /* Generate New Eway Bill */
    public function generateEwayBill($data){
        $ref_id = $data['ref_id'];
        $document_type = $data['document_type'];
        $postData = $this->ewbJsonSingle($data);

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/ewayBill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):			
			$ewayLog = [
                'id' => '',
                'type' => 'EWB',
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => 'EWB',
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => 'EWB',
                    'response_status'=> "Success",
                    'eway_bill_no' => $responseEwaybill->data->eway_bill_no,
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $effectiveTable = ($document_type == "JOBWORK")?"outsource":"trans_main";
                $this->edit($effectiveTable,['id'=>$data['ref_id']],['ewb_status'=>((!empty($responseEwaybill->data->eway_bill_no))?1:0),'eway_bill_no'=>$responseEwaybill->data->eway_bill_no]);

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }

    /* SYNC Eway Bill Data From GOV. Portal */
    public function syncEwayBill($data){
        $ref_id = $data['ref_id'];
        $document_type = $data['document_type'];
        $postData = $this->ewbJsonSingle($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/syncEwayBill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):			
			$ewayLog = [
                'id' => '',
                'type' => "SYNCEWB",
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "SYNCEWB",
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "SYNCEWB",
                    'response_status'=> "Success",
                    'eway_bill_no'=>$responseEwaybill->data->eway_bill_no,
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $ewbStatus = (!empty($responseEwaybill->data->cancel_reason))?3:((!empty($responseEwaybill->data->eway_bill_no))?2:0);

                $effectiveTable = ($document_type == "JOBWORK")?"outsource":"trans_main";
                $this->edit($effectiveTable,['id'=>$data['ref_id']],['ewb_status'=>$ewbStatus,'eway_bill_no'=>$responseEwaybill->data->eway_bill_no,'close_reason'=>$cancel_reason,'close_date'=>(!empty($responseEwaybill->data->cancel_date))?$responseEwaybill->data->cancel_date:NULL]);

                return ['status'=>1,'message'=>'E-way Bill SYNC successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    /* Cancel Eway Bill By Eway Bill No. */
    public function cancelEwayBill($postData){
        $ref_id = $postData['ref_id'];
        $document_type = $postData['document_type'];

        $orgData = $this->getCompanyInfo();
        $postData['Gstin'] = $orgData->company_gst_no;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/cancelEwayBill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):			
			$ewayLog = [
                'id' => '',
                'type' => "CNLEWB",
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "CNLEWB",
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "CNLEWB",
                    'response_status'=> "Success",
                    'eway_bill_no' => $postData['ewbNo'],
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $cancel_date = (!empty($responseEwaybill->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEwaybill->data->cancel_date)):NULL;

                $effectiveTable = ($document_type == "JOBWORK")?"outsource":"trans_main";
                $this->edit($effectiveTable,['id'=>$ref_id],['ewb_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date]);

                return ['status'=>1,'message'=>'E-way Bill Cancel successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    /* Download Eway Bill Json */
    public function generateEwbJson($data){
        $jsonData = $this->ewbJsonSingle($data);

        $invNo = $jsonData['ewbData']['docNo'];
        return ['status'=>1,'message'=>'Json Generated successfully.','json_data'=>$jsonData['ewbData'],'inv_no'=>$invNo];
    }

    /* Generate Einvoice Json */
    public function einvJson($data){
        $postData = array();
		$ref_id = $data['ref_id'];

        if($data['doc_type'] == "INV"):
			$invData = $this->salesInvoice->getSalesInvoice(['id'=>$ref_id,'itemList'=>1]);
		elseif($data['doc_type'] == "CRN"):
			$invData = $this->creditNote->getCreditNote(['id'=>$ref_id,'itemList'=>1]);
		elseif($data['doc_type'] == "DBN"):
			$invData = $this->debitNote->getDebitNote(['id'=>$ref_id,'itemList'=>1]);
		endif;
        $invData = $this->calculateInvoiceValue($invData);
        
        $orgData = $this->getCompanyInfo();
        $partyData = $this->party->getParty(['id'=>$invData->party_id]);
        $shipToData = [];
        if($data['doc_type'] == "INV" && !empty($invoiceData->ship_to)):
            $shipToData = $this->party->getParty(['id'=>$invData->ship_to]);
        endif;

        if(!empty($data['trans_distance'])):
            $this->edit('party_master',['id'=>$invData->party_id],['distance'=>$data['trans_distance']]);
        endif;

        $disCityData = $this->party->getState(['id'=>$data['dispatch_state']]);
        $bilCityData = $this->party->getState(['id'=>$data['billing_state']]);
        $shipCityData = $this->party->getState(['id'=>$data['ship_state']]);

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_phone,
            'contact_no' => $orgData->company_phone,
            'country_name' => $orgData->company_country_name,
            'state_name' => $orgData->company_state_name,
            'city_name' => $orgData->company_city_name,
            'address' => $orgData->company_address,
            'pincode' => $orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];
        
        $postData['partyInfo'] = [
            'name' => $partyData->party_name,
            'gst_no' => (!empty($partyData->gstin))?$partyData->gstin:"URP",
            'pan_no' => $partyData->pan_no,            
            'email' => $partyData->party_email,
            'contact_email' => $partyData->party_email,
            'phone_no' => $partyData->party_mobile,
            'contact_no' => $partyData->party_mobile,
            'billing_address' => str_replace('"',"",((!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($data['billing_city']))?$bilCityData->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($data['billing_city']))?$bilCityData->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($data['billing_city']))?$data['billing_city']:$partyData->city_name,
            'billing_state_code' => $bilCityData->state_code,
            'ship_address' => str_replace('"',"",((!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($data['ship_city']))?$shipCityData->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($data['ship_city']))?$shipCityData->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($data['ship_city']))?$data['ship_city']:$partyData->city_name,
            'ship_state_code' => $shipCityData->state_code,
        ];        

        $einvData = array();
        $einvData["Version"] = "1.1";

        $einvData["TranDtls"] = [
            "TaxSch" => "GST", 
            "SupTyp" => $data['type_of_transaction'], 
            "RegRev" => "N", 
            "EcmGstin" => null, 
            "IgstOnIntra" => "N" 
        ];

        $einvData["DocDtls"] = [
            "Typ" => $data['doc_type'], 
            "No" => $invData->trans_number, 
            "Dt" => date("d/m/Y",strtotime($invData->trans_date))
        ];

        $orgData->company_address = str_replace(["\r\n", "\r", "\n"], " ", $orgData->company_address);
        $orgAdd1 = substr($orgData->company_address,0,100);
        $orgAdd2 = (strlen($orgData->company_address) > 100)?substr($orgData->company_address,100,200):"";
        $orgData->company_phone = str_replace(["+"," ","-"],"",$orgData->company_phone);
        $einvData["SellerDtls"] = [
            "Gstin" => $orgData->company_gst_no, 
            "LglNm" => $orgData->company_name,
            "TrdNm" => $orgData->company_name, 
            "Addr1" => $orgAdd1, 
            "Loc" => $orgData->company_city_name,  
            "Pin" => (int) $orgData->company_pincode,
            "Stcd" => $orgData->company_state_code
        ];
        if(!empty($orgData->company_phone)):
            $einvData["SellerDtls"]['Ph'] = trim(str_replace(["+","-"," "],"",$orgData->company_phone));
        endif;
        if(!empty($orgData->company_email)):
            $einvData["SellerDtls"]['Em'] = $orgData->company_email;
        endif;
        if(strlen($orgAdd2)):
            $einvData["SellerDtls"]['Addr2'] = $orgAdd2;
        endif;

        $billingAddress = (!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address;
        $billingAddress = str_replace(["\r\n", "\r", "\n"], " ", $billingAddress);
        $partyAdd1 = substr($billingAddress,0,100);
        $partyAdd2 = (strlen($billingAddress) > 100)?substr($billingAddress,100,200):"";
        $billingPincode = (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode;
        $einvData["BuyerDtls"] = [
            "Gstin" => (!empty($partyData->gstin))?$partyData->gstin:"URP",//$partyData->gstin, 
            "LglNm" => $partyData->party_name, 
            "TrdNm" => $partyData->party_name,
            "Pos" => $bilCityData->state_code, 
            "Addr1" => $partyAdd1, 
            "Loc" => (!empty($data['billing_city']))?$data['billing_city']:$partyData->city_name, 
            "Pin" => (int) $billingPincode, 
            "Stcd" => $bilCityData->state_code
        ];
        if(!empty($partyData->party_mobile)):
            $einvData["BuyerDtls"]['Ph'] = trim(str_replace(["+","-"," "],"",$partyData->party_mobile));
        endif;
        if(!empty($partyData->party_email)):
            $einvData["BuyerDtls"]['Em'] = $partyData->party_email;
        endif;
        if(strlen($partyAdd2) > 3):
            $einvData["BuyerDtls"]['Addr2'] = $partyAdd2;
        endif;

        $dispatchAddress = (!empty($data['dispatch_address']))?$data['dispatch_address']:$orgData->company_address;
        $dispatchAddress = str_replace(["\r\n", "\r", "\n"], " ", $dispatchAddress);
        $dispatchAdd1 = substr($dispatchAddress,0,100);
        $dispatchAdd2 = (strlen($dispatchAddress) > 100)?substr($dispatchAddress,100,200):"";
        $dispatchPincode = (!empty($data['dispatch_pincode']))?$data['dispatch_pincode']:$orgData->company_pincode;
        $einvData["DispDtls"] = [
            "Nm" => $orgData->company_name,
            "Addr1" => $dispatchAdd1,  
            "Loc" => (!empty($data['dispatch_city']))?$data['dispatch_city']:$orgData->company_city_name,  
            "Pin" => (int) $dispatchPincode,
            "Stcd" => $disCityData->state_code, 
        ];
        if(strlen($dispatchAdd2)):
            $einvData["DispDtls"]['Addr2'] = $dispatchAdd2;
        endif;

        $shippingAddress = (!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address;
        $shippingAddress = str_replace(["\r\n", "\r", "\n"], " ", $shippingAddress);
        $shipAdd1 = substr($shippingAddress,0,100);
        $shipAdd2 = (strlen($shippingAddress) > 100)?substr($shippingAddress,100,200):"";
        $shipCode = (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode;
        $einvData["ShipDtls"] = [
            "Gstin" => (!empty($shipToData->gstin))?$shipToData->gstin:((!empty($partyData->gstin))?$partyData->gstin:"URP"),//$partyData->gstin,
            "LglNm" => (!empty($shipToData->party_name))?$shipToData->party_name:$partyData->party_name,
            "TrdNm" => (!empty($shipToData->party_name))?$shipToData->party_name:$partyData->party_name, 
            "Addr1" => $shipAdd1, 
            "Loc" => (!empty($data['ship_city']))?$data['ship_city']:$partyData->city_name,
            "Pin" => (int) $shipCode,  
            "Stcd" => $shipCityData->state_code
        ];
        if(strlen($shipAdd2) > 3):
            $einvData["ShipDtls"]['Addr2'] = $shipAdd2;
        endif;

        $i=1;
        foreach($invData->itemList as $row):
            $row->item_name = str_replace(['"'], ' ', $row->item_name);
            $einvData["ItemList"][] = [
                "SlNo" => strval($i++), 
                "PrdDesc" => $row->item_name, 
                "IsServc" => (in_array($invData->tax_class,['SALESJOBGSTACC','SALESJOBIGSTACC','PURJOBGSTACC','PURJOBIGSTACC']))?"Y":(($row->item_type == 10)?"Y":"N"),
                "HsnCd" => $row->hsn_code,
                // "Barcde" => "123456", 
                "Qty" => round($row->qty,2), 
                "FreeQty" => 0, 
                "Unit" => $row->unit_name, 
                "UnitPrice" => round($row->price,2), 
                "TotAmt" => round($row->amount,2), 
                "Discount" => round($row->disc_amount,2), 
                // "PreTaxVal" => 1, 
                "AssAmt" => round($row->taxable_amount,2), 
                "GstRt" => round($row->gst_per,2), 
                "IgstAmt" => ($invData->gst_type == 2)?round($row->igst_amount,2):0, 
                "CgstAmt" => ($invData->gst_type == 1)?round($row->cgst_amount,2):0, 
                "SgstAmt" => ($invData->gst_type == 1)?round($row->sgst_amount,2):0, 
                // "CesRt" => 5, 
                // "CesAmt" => 498.94, 
                // "CesNonAdvlAmt" => 10, 
                // "StateCesRt" => 12, 
                // "StateCesAmt" => 1197.46, 
                // "StateCesNonAdvlAmt" => 5, 
                // "OthChrg" => 10, 
                "TotItemVal" => round($row->net_amount,2), 
                // "OrdLineRef" => "3256", 
                // "OrgCntry" => "AG", 
                // "PrdSlNo" => "12345", 
                // "BchDtls" => [
                //     "Nm" => "123456", 
                //     "Expdt" => "01/08/2020", 
                //     "wrDt" => "01/09/2020" 
                // ], 
                // "AttribDtls" => [
                //     [
                //         "Nm" => "Rice", 
                //         "Val" => "10000" 
                //     ] 
                // ] 
            ];
        endforeach;
        
        $otherCharge = round((($invData->net_amount + ($invData->round_off_amount * -1)) - ($invData->taxable_amount + $invData->igst_amount + $invData->cgst_amount + $invData->sgst_amount)),2);

        $einvData["ValDtls"] = [
            "AssVal" => round($invData->taxable_amount,2),
            "CgstVal" => ($invData->gst_type == 1)?round($invData->cgst_amount,2):0, 
            "SgstVal" => ($invData->gst_type == 1)?round($invData->sgst_amount,2):0, 
            "IgstVal" => ($invData->gst_type == 2)?round($invData->igst_amount,2):0,
            // "CesVal" => 508.94, 
            // "StCesVal" => 1202.46, 
            // "Discount" => floatVal($row->disc_amount), 
            "OthChrg" => (($otherCharge > 0)?$otherCharge:0),
            "RndOffAmt" => round($invData->round_off_amount,2), 
            "TotInvVal" => round($invData->net_amount,2), 
            // "TotInvValFc" => 12897.7
        ];

        /* $einvData["PayDtls"] = [
            "Nm" => "ABCDE", 
            "Accdet" => "5697389713210", 
            "Mode" => "Cash", 
            "Fininsbr" => "SBIN11000", 
            "Payterm" => "100", 
            "Payinstr" => "Gift", 
            "Crtrn" => "test", 
            "Dirdr" => "test", 
            "Crday" => 100, 
            "Paidamt" => 10000, 
            "Paymtdue" => 5000
        ]; */

        /* $einvData["RefDtls"] = [
            "InvRm" => "TEST", 
            "DocPerdDtls" => [
                "InvStDt" => "01/08/2020", 
                "InvEndDt" => "01/09/2020" 
            ], 
            "PrecDocDtls" => [
                [
                    "InvNo" => "DOC/002", 
                    "InvDt" => "01/08/2020", 
                    "OthRefNo" => "123456" 
                ] 
            ], 
            "ContrDtls" => [
                [
                    "RecAdvRefr" => "Doc/003", 
                    "RecAdvDt" => "01/08/2020", 
                    "Tendrefr" => "Abc001", 
                    "Contrrefr" => "Co123", 
                    "Extrefr" => "Yo456", 
                    "Projrefr" => "Doc-456", 
                    "Porefr" => "Doc-789", 
                    "PoRefDt" => "01/08/2020" 
                ] 
            ]
        ]; */

        /* $einvData["AddlDocDtls"] = [
            [
                "Url" => "https://einv-apisandbox.nic.in", 
                "Docs" => "Test Doc", 
                "Info" => "Document Test" 
            ]
        ]; */

        if(in_array($invData->tax_class,['EXPORTGSTACC','EXPORTTFACC'])):
            $einvData["ExpDtls"] = [
                "ShipBNo" => $invData->ship_bill_no, 
                "ShipBDt" => date("d/m/Y",strtotime($invData->ship_bill_date)), 
                "Port" => $invData->port_code
            ];
        endif;

        if($data['ewb_status'] == 1):
            $einvData["EwbDtls"]["Distance"] = intVal($data['trans_distance']);

            if(!empty($data['transport_id'])):
                $einvData["EwbDtls"]["TransId"] = $data['transport_id'];
            endif;

            if(!empty($data['transport_name'])):
                $einvData["EwbDtls"]["TransName"] = $data['transport_name'];
            endif;
            
            if(!empty($data['transport_doc_no'])):
                $einvData["EwbDtls"]["TransDocNo"] = $data['transport_doc_no'];
            endif;

            if(!empty($data['transport_doc_date'])):
                $einvData["EwbDtls"]["TransDocDt"] = (!empty($data['transport_doc_date']))?date("d/m/Y",strtotime($data['transport_doc_date'])):"";
            endif;

            if(!empty($data['vehicle_no'])):
                $einvData["EwbDtls"]["VehNo"] = $data['vehicle_no'];
            endif;

            if(!empty($data['vehicle_type'])):
                $einvData["EwbDtls"]["VehType"] = $data['vehicle_type'];
            endif;

            if(!empty($data['trans_mode'])):
                $einvData["EwbDtls"]["TransMode"] = $data['trans_mode'];
            endif;
        endif;

        $postData['einvData'] = $einvData;
        return $postData;
    }

    //Recalculate Invoice Values
    public function calculateInvoiceValue($invData){
        foreach($invData->itemList as &$row):
            $row->taxable_amount += $row->exp_taxable_amount;
            $row->gst_amount += $row->exp_gst_amount;
            $row->amount = $row->taxable_amount + $row->disc_amount;
            $row->price = round(($row->amount / $row->qty),2);
            
            $row->cgst_amount = $row->sgst_amount = $row->igst_amount = 0;
            $row->cgst_per = $row->sgst_per = $row->igst_per = 0;
            if($invData->gst_type == 1):
                $row->cgst_per = $row->sgst_per = round(($row->gst_per / 2),2);
                $row->cgst_amount = $row->sgst_amount = round(($row->gst_amount / 2),2);                               
            elseif($invData->gst_type == 2):
                $row->igst_per = $row->gst_per;
                $row->igst_amount = $row->gst_amount;
            endif;

            $row->net_amount = $row->taxable_amount + $row->gst_amount;
        endforeach;

        $invData->taxable_amount = $invData->taxable_amount + array_sum(array_column($invData->itemList,'exp_taxable_amount'));

        return $invData;
    }

    /* Generate New E-Invoice */
    public function generateEinvoice($data){
        $ref_id = $data['ref_id'];
        $postData = $this->einvJson($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/eInvoice",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):			
			$ewayLog = [
                'id' => '',
                'type' => "EINV",
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "EINV",
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "EINV",
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);


                $updateData = ['e_inv_status'=>((!empty($responseEinv->data->ack_no))?1:0),'e_inv_no'=>$responseEinv->data->ack_no,'e_inv_irn'=>$responseEinv->data->irn,'e_inv_qr_code'=>$responseEinv->data->e_inv_qr_code,'e_inv_date'=>$responseEinv->data->ack_date];
                
                if(!empty($responseEinv->data->eway_bill_no)):
                    $updateData['eway_bill_no'] = $responseEinv->data->eway_bill_no;
                endif;

                $this->edit("trans_main",['id'=>$ref_id],$updateData);

                return ['status'=>1,'message'=>'E-Invoice Generated successfully.'];
            endif;
        endif;
    }

    /* SYNC E-Invoice From GOV. Portal */
    public function syncEinvoice($data){
        $ref_id = $data['ref_id'];
        $doc_type = $data['doc_type'];
        $postData = $this->einvJson($data);

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/syncEinv",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):			
			$ewayLog = [
                'id' => '',
                'type' => "SYNCEINV",
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "SYNCEINV",
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "SYNCEINV",
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                $einvStatus = (!empty($responseEinv->data->cancel_reason))?3:2;

                $updateData = [
                    'e_inv_status'=>$einvStatus,
                    'e_inv_no'=>$responseEinv->data->ack_no,
                    'e_inv_irn'=>$responseEinv->data->irn,
                    'e_inv_qr_code'=>$responseEinv->data->e_inv_qr_code,
                    'e_inv_date'=>$responseEinv->data->ack_date,
                    'close_reason'=>$cancel_reason,
                    'close_date'=>(!empty($responseEinv->data->cancel_date))?$responseEinv->data->cancel_date:NULL
                ];

                if(!empty($responseEinv->data->eway_bill_no)):
                    $updateData['eway_bill_no'] = $responseEinv->data->eway_bill_no;
                endif;

                if($einvStatus == 3):
                    $updateData['trans_status'] = 3;

                    if($doc_type == "INV"):
                        $this->reverseInvoice($ref_id);
                    elseif($doc_type == "CRN"):
                        $this->reverseCreditNote($ref_id);
                    elseif($doc_type == "DBN"):
                        $this->reverseDebitNote($ref_id);
                    endif;
                endif;

                $this->edit("trans_main",['id'=>$ref_id],$updateData);

                return ['status'=>1,'message'=>'E-Invoice Sync successfully.','data'=>$responseEinv];
            endif;
        endif;
    }

    /* Cancel E-Invoice on irn */
    public function cancelEinv($postData){
        $ref_id = $postData['ref_id'];
        $doc_type = $postData['doc_type'];
        unset($postData['doc_type']);

        $calcelReason = [
            1 => "Duplicate", 
            2 => "Data entry mistake", 
            3 => "Order Cancelled", 
            4 => "Others"
        ];

        if(!empty($postData['Irn'])):
            if($doc_type == "INV"):
                $invData = $this->salesInvoice->getSalesInvoice(['id'=>$ref_id,'itemList'=>0]);
            elseif($doc_type == "CRN"):
                $invData = $this->creditNote->getCreditNote(['id'=>$ref_id,'itemList'=>0]);
            elseif($doc_type == "DBN"):
                $invData = $this->debitNote->getDebitNote(['id'=>$ref_id,'itemList'=>0]);
            endif;
            
            $orgData = $this->getCompanyInfo();
            
            $postData['Gstin'] = $orgData->company_gst_no;

            $curlEwaybill = curl_init();
            curl_setopt_array($curlEwaybill, array(
                CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/cancelEinv",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_POSTFIELDS => json_encode($postData)
            ));

            $response = curl_exec($curlEwaybill);
            $error = curl_error($curlEwaybill);
            curl_close($curlEwaybill);

            if($error):			
                $ewayLog = [
                    'id' => '',
                    'type' => "CNLEINV",
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
            else:
                $responseEinv = json_decode($response,false);	
                            
                if(isset($responseEinv->status) && $responseEinv->status == 0):				
                    $ewayLog = [
                        'id' => '',
                        'type' => "CNLEINV",
                        'response_status'=> "Fail",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->eBillLog,$ewayLog);
                    
                    return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
                else:						
                    $ewayLog = [
                        'id' => '',
                        'type' => "CNLEINV",
                        'response_status'=> "Success",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->eBillLog,$ewayLog);
                    
                    $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                    $cancel_date = (!empty($responseEinv->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEinv->data->cancel_date)):NULL;

                    $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);

                    if($doc_type == "INV"):
                        $this->reverseInvoice($ref_id);
                    elseif($doc_type == "CRN"):
                        $this->reverseCreditNote($ref_id);
                    elseif($doc_type == "DBN"):
                        $this->reverseDebitNote($ref_id);
                    endif;

                    return ['status'=>1,'message'=>'E-Invoice Cancel successfully.','data'=>$responseEinv];
                endif;
            endif;
        else:
            $cancel_reason = $calcelReason[$postData['CnlRsn']]." - ".$postData['CnlRem'];
            $cancel_date = date("Y-m-d H:i:s");

            $this->edit("trans_main",['id'=>$ref_id],['close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);

            if($doc_type == "INV"):
                $this->reverseInvoice($ref_id);
                return ['status'=>1,'message'=>'Tax Invoice Cancel successfully.'];
            elseif($doc_type == "CRN"):
                $this->reverseCreditNote($ref_id);
                return ['status'=>1,'message'=>'Credit Note Cancel successfully.'];
            elseif($doc_type == "DBN"):
                $this->reverseDebitNote($ref_id);
                return ['status'=>1,'message'=>'Debit Note Cancel successfully.'];
            endif;           
        endif;
    }

    /* If Cancel invoice then reverse Stock effects and ledger effect */
    public function reverseInvoice($ref_id){
        $dataRow = $this->salesInvoice->getSalesInvoice(['id'=>$ref_id,'itemList'=>1]);

        foreach($dataRow->itemList as $row):
            if(!empty($row->ref_id)):
                $setData = array();
                $setData['tableName'] = "trans_child";
                $setData['where']['id'] = $row->ref_id;
                $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                $this->setValue($setData);
            endif;
        endforeach;

        if(!empty($dataRow->ref_id)):
            $oldRefIds = explode(",",$dataRow->ref_id);
            foreach($oldRefIds as $main_id):
                $setData = array();
                $setData['tableName'] = "trans_main";
                $setData['where']['id'] = $main_id;
                $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                $this->setValue($setData);
            endforeach;
        endif;

        $this->remove("stock_transaction",['main_ref_id'=>$dataRow->id,'entry_type'=>$dataRow->entry_type]);
        $this->transMainModel->deleteLedgerTrans($dataRow->id);

        return true;
    }

    /* If Cancel Credit Note then reverse Stock effects and ledger effect */
    public function reverseCreditNote($ref_id){
        $vouData = $this->creditNote->getCreditNote(['id'=>$ref_id,'itemList'=>1]);

        if(!empty($vouData->ref_id)):
            $setData = array();
            $setData['tableName'] = "trans_main";
            $setData['where']['id'] = $vouData->ref_id;
            $setData['set_value']['rop_amount'] = 'IF(`rop_amount` - '.$vouData->net_amount.' >= 0, `rop_amount` - '.$vouData->net_amount.', 0)';
            $this->setValue($setData);
        endif;

        foreach($vouData->itemList as $row):
            if($row->stock_eff == 1 && !empty($row->ref_id)):
                $setData = array();
                $setData['tableName'] = "trans_child";
                $setData['where']['id'] = $row->ref_id;
                $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                $this->setValue($setData);
            endif;
        endforeach;

        $this->remove("stock_transaction",['main_ref_id'=>$vouData->id,'entry_type'=>$vouData->entry_type]);
        $this->transMainModel->deleteLedgerTrans($vouData->id);

        return true;
    }

    /* If Cancel Debit Note then reverse Stock effects and ledger effect */
    public function reverseDebitNote($ref_id){
        $vouData = $this->debitNote->getDebitNote(['id'=>$ref_id,'itemList'=>1]);

        if(!empty($vouData->ref_id)):
            $setData = array();
            $setData['tableName'] = "trans_main";
            $setData['where']['id'] = $vouData->ref_id;
            $setData['set_value']['rop_amount'] = 'IF(`rop_amount` - '.$vouData->net_amount.' >= 0, `rop_amount` - '.$vouData->net_amount.', 0)';
            $this->setValue($setData);
        endif;

        foreach($vouData->itemList as $row):
            if($row->stock_eff == 1 && !empty($row->ref_id)):
                $setData = array();
                $setData['tableName'] = "trans_child";
                $setData['where']['id'] = $row->ref_id;
                $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                $this->setValue($setData);
            endif;
        endforeach;

        $this->remove("stock_transaction",['main_ref_id'=>$vouData->id,'entry_type'=>$vouData->entry_type]);
        $this->transMainModel->deleteLedgerTrans($vouData->id);

        return true;
    }

    /* Download E-Invoice Json */
    public function generateEinvJson($data){
        $jsonData = $this->einvJson($data);
        /* $validate = $this->validateEinvoiceJson($jsonData['einvData']);
        if($validate['status'] == 2):
            return $validate;
        endif; */

        $invNo = $jsonData['einvData']['DocDtls']['No'];
        return ['status'=>1,'message'=>'Json Generated successfully.','json_data'=>$jsonData['einvData'],'inv_no'=>$invNo];
    }
    
    /* Get Ebill Data */
    public function getEmasterData($no,$type){
        $queryData = array();
        $queryData['tableName'] = $this->eBillLog;
        $queryData['where_in']['type'] = $type;

        if(in_array($type,["EWB","SYNCEWB","'EWB','SYNCEWB'"])):
            $queryData['where']['eway_bill_no'] = $no;
        else:
            $queryData['where']['ack_no'] = $no;
        endif;
        $queryData['order_by']['id'] = 'DESC';
        $queryData['limit'] = 1;        
        $result = $this->row($queryData);

        $response = json_decode($result->response_data,false);
        $response = $response->data;
        //print_r($response);exit;
        $response->json_data = json_decode($response->json_data,false);  
        $response->response_json = json_decode($response->response_json,false);      
        $response->company_info = json_decode($response->company_info,false);
        $response->party_info = json_decode($response->party_info,false);
        //print_r($response->json_data);exit;

        return $response;
    }

    /* Verify Gst No. */
    public function getGstinDetail($postData){
        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/getGstinDetail",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):	
            $ewayLog = [
                'id' => '',
                'type' => 'VERGST',
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
            
            return ['status'=>2,'message'=>'Somthing is wrong. cURL Error #:'. $error]; 
        else:
            $responseGstin = json_decode($response);	
                        
            if(isset($responseGstin->status) && $responseGstin->status == 0):
                $ewayLog = [
                    'id' => '',
                    'type' => 'VERGST',
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong. GSTIN Error #: '. $responseGstin->error_message,'data'=>$responseGstin];
            else:		
                $ewayLog = [
                    'id' => '',
                    'type' => 'VERGST',
                    'response_status'=> "Success",
                    'eway_bill_no' => "",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $queryData = array();
                $queryData['tableName'] = "states";
                $queryData['where']['gst_statecode'] = $responseGstin->data->StateCode;
                $queryData['where']['country_id'] = 101;
                $stateData = $this->row($queryData);

                $queryData = array();
                $queryData['tableName'] = "cities";
                $queryData['where']['TRIM(LOWER(name))'] = trim(strtolower($responseGstin->data->AddrLoc));
                if(!empty($stateData)):
                    $queryData['where']['state_id'] = $stateData->id;
                endif;
                $queryData['where']['country_id'] = 101;
                $cityData = $this->row($queryData);

                $returnData = [
                    'party_name' => $responseGstin->data->TradeName,
                    'ledger_name' => $responseGstin->data->LegalName,
                    'party_address' => $responseGstin->data->AddrBnm." ".$responseGstin->data->AddrBno." ".$responseGstin->data->AddrFlno." ".$responseGstin->data->AddrSt." ".$responseGstin->data->AddrLoc." ".$responseGstin->data->AddrPncd,
                    'party_pincode' => $responseGstin->data->AddrPncd,
                    'party_state_code' => $responseGstin->data->StateCode,
                    'country_id' => 101,
                    'country_name' => "India",
                    'state_id' => (!empty($stateData))?$stateData->id:0,
                    'state_name' => (!empty($stateData))?$stateData->name:"",
                    'city_id' => (!empty($cityData))?$cityData->id:0,
                    'city_name' => (!empty($cityData))?$cityData->name:"",
                    'village_name' => $responseGstin->data->AddrLoc,
                    'pan_no' =>  substr($responseGstin->data->Gstin,2,10),
                    'gst_reg_date' => $responseGstin->data->DtReg
                ];

                return ['status'=>1,'message'=>'GSTIN verified successfully.',"data"=>$returnData];
            endif;
        endif;
    }

    /* Get Company Wise Point To Point Distance */
    public function getPinToPinDistance($postData){
        $cm_ids = (!empty($postData['cm_ids']))?$postData['cm_ids']:[1,2,3];

        $companyList = $this->masterModel->getCompanyList($cm_ids);
        $distance_cm_1 = $distance_cm_2 = $distance_cm_3 = 0;
        foreach($companyList as $row):
            $postData['cm_id'] = $row->id;
            $postData['Gstin'] = $row->company_gst_no;
            $postData['startPoint'] = $row->company_pincode;
            $result = $this->getPointToPointDistance($postData);

            if($result['status'] == 1):
                if($postData['cm_id'] == 1):
                    $distance_cm_1 = $result['distance'];
                elseif($postData['cm_id'] == 2):
                    $distance_cm_2 = $result['distance'];
                elseif($postData['cm_id'] == 3):
                    $distance_cm_3 = $result['distance'];
                endif;
            endif;
        endforeach;

        return ['status'=>1,'message'=>'Point To Point Distance fetched successfully.','distance_cm_1'=>$distance_cm_1,'distance_cm_2'=>$distance_cm_2,'distance_cm_3'=>$distance_cm_3];
    }

    /* Find Point To Point Distance */
    public function getPointToPointDistance($postData){
        $curlPTPD = curl_init();
        curl_setopt_array($curlPTPD, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/getPointToPointDistance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($curlPTPD);
        $error = curl_error($curlPTPD);
        curl_close($curlPTPD);

        if($error):	
            $ewayLog = [
                'id' => '',
                'type' => 'PTPD',
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
            
            return ['status'=>2,'message'=>'Somthing is wrong. cURL Error #:'. $error]; 
        else:
            $responsePTPD = json_decode($response);	
                        
            if(isset($responsePTPD->status) && $responsePTPD->status == 0):
                $ewayLog = [
                    'id' => '',
                    'type' => 'PTPD',
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong. Point To Point Distance Error #: ','data'=>$responsePTPD];
            else:		
                $ewayLog = [
                    'id' => '',
                    'type' => 'PTPD',
                    'response_status'=> "Success",
                    'eway_bill_no' => "",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $setData = array();
                $setData['tableName'] = "trans_details";
                $setData['where']['id'] = $postData['ship_to_id'];
                if($postData['cm_id'] == 1):
                    $setData['update']['i_col_4'] = $responsePTPD->distance;
                elseif($postData['cm_id'] == 2):
                    $setData['update']['i_col_5'] = $responsePTPD->distance;
                elseif($postData['cm_id'] == 3):
                    $setData['update']['i_col_6'] = $responsePTPD->distance;
                endif;
                $this->setValue($setData);

                return ['status'=>1,'message'=>'Point To Point Distance fetched successfully.',"distance"=>$responsePTPD->distance,'data'=>$responsePTPD];
            endif;
        endif;
    }
}
?>
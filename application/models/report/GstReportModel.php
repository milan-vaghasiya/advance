<?php
class GstReportModel extends MasterModel{

    public function _b2b($data){
        $party_id = (!empty($data['party_id']))?" AND trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gstin,trans_main.party_state_code,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.itc,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin != 'URP'
            AND trans_main.tax_class NOT IN ('IMPORTACC','IMPORTSACC','SEZRACC','EXPORTGSTACC','EXPORTTFACC','PURTFACC','PURCTFACC','PUREXEMPTEDTFACC','PURCEXEMPTEDTFACC','PURNONGST','PURCNONGST','SALESTFACC','SALESCTFACC','SALESEXEMPTEDTFACC','SALESCEXEMPTEDTFACC','SALESNONGST','SALESCNONGST')
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.id
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2bur($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_main.party_state_code,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.itc
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.tax_class NOT IN ('IMPORTACC','IMPORTSACC','SEZRACC','EXPORTGSTACC','EXPORTTFACC','PURTFACC','PURCTFACC','PUREXEMPTEDTFACC','PURCEXEMPTEDTFACC','PURNONGST','PURCNONGST','SALESTFACC','SALESCTFACC','SALESEXEMPTEDTFACC','SALESCEXEMPTEDTFACC','SALESNONGST','SALESCNONGST')
            AND trans_main.gstin = 'URP'
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.doc_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2ba($data){
        return [];
    }

    public function _b2cl($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $companyInfo = $this->getCompanyInfo();

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.gstin,states.gst_statecode as party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.tax_class NOT IN ('IMPORTACC','IMPORTSACC','SEZRACC','EXPORTGSTACC','EXPORTTFACC','PURTFACC','PURCTFACC','PUREXEMPTEDTFACC','PURCEXEMPTEDTFACC','PURNONGST','PURCNONGST','SALESTFACC','SALESCTFACC','SALESEXEMPTEDTFACC','SALESCEXEMPTEDTFACC','SALESNONGST','SALESCNONGST')
            AND trans_main.trans_status != 3
            AND trans_main.party_state_code != ".$companyInfo->company_state_code."
            ".$party_id."
            AND trans_main.taxable_amount > 250000
            GROUP BY trans_child.gst_per,trans_main.trans_number
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2cla($data){
        return [];
    }

    public function _b2cs($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $companyInfo = $this->getCompanyInfo();

        $result = $this->db->query("
            SELECT trans_child.gst_per,
            SUM((trans_child.taxable_amount * trans_main.p_or_m) * -1) as taxable_amount,
            SUM(trans_child.cess_amount) as cess_amount,
            states.name as state_name,states.gst_statecode as party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.tax_class NOT IN ('IMPORTACC','IMPORTSACC','SEZRACC','EXPORTGSTACC','EXPORTTFACC','PURTFACC','PURCTFACC','PUREXEMPTEDTFACC','PURCEXEMPTEDTFACC','PURNONGST','PURCNONGST','SALESTFACC','SALESCTFACC','SALESEXEMPTEDTFACC','SALESCEXEMPTEDTFACC','SALESNONGST','SALESCNONGST')
            AND trans_main.trans_status != 3
            ".$party_id."
            AND ((trans_main.taxable_amount <= 250000 AND trans_main.party_state_code != ".$companyInfo->company_state_code.") OR trans_main.party_state_code = ".$companyInfo->company_state_code.")
            GROUP BY trans_child.gst_per,states.gst_statecode 
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _b2csa($data){
        return [];
    }

    public function _cdnr($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $orderType = ($data['report'] == "gstr1")?"'Increase Sales','Decrease Sales','Sales Return'":"'Increase Purchase','Decrease Purchase','Purchase Return'";
        $companyInfo = $this->getCompanyInfo();

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.gstin,trans_main.party_state_code,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin != 'URP'
            AND trans_main.order_type IN (".$orderType.")
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _cdnra($data){
        return [];
    }

    public function _cdnur($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $orderType = ($data['report'] == "gstr1")?"'Increase Sales','Decrease Sales','Sales Return'":"'Increase Purchase','Decrease Purchase','Purchase Return'";
        $taxClass = ($data['report'] == 'gstr1')?"'SALESGSTACC','SALESIGSTACC','SALESJOBGSTACC','SALESJOBIGSTACC','SALESTFACC','SALESEXEMPTEDTFACC','EXPORTGSTACC','EXPORTTFACC'":"'PURURDGSTACC','PURURDIGSTACC','PURTFACC','PUREXEMPTEDTFACC'";
        

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.gstin,states.gst_statecode as party_state_code,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.order_type IN (".$orderType.")
            AND trans_main.trans_status != 3
            AND trans_main.taxable_amount > 250000
            ".$party_id."
            
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _cdnura($data){
        return [];
    }

    public function _exp($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.gstin,trans_main.tax_class,trans_main.port_code,trans_main.ship_bill_no,trans_main.ship_bill_date,trans_main.sales_type,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.tax_class IN ('EXPORTGSTACC','EXPORTTFACC')
            AND trans_main.trans_status != 3
            ".$party_id."
            
            GROUP BY trans_child.gst_per,trans_main.trans_number
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _expa($data){
        return [];
    }

    public function _at($data){
        return [];
    }

    public function _ata($data){
        return [];
    }

    public function _atadj($data){
        return [];
    }

    public function _atadja($data){
        return [];
    }

    public function _exemp($data){
        
        if($data['report'] == "gstr1"):
            $sql = "
                SELECT 'Inter-State supplies to registered persons' as description,

                SUM((CASE WHEN trans_main.tax_class = 'SALESTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as nill_rate_amount, 

                SUM((CASE WHEN trans_main.tax_class = 'SALESEXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as exe_amount,

                SUM((CASE WHEN trans_main.tax_class = 'SALESNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as non_gst_amount

                FROM trans_child
                LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
                WHERE trans_child.is_delete = 0
                AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
                AND trans_main.gstin != 'URP'
                AND trans_main.trans_status != 3
                
                
                UNION ALL

                SELECT 'Intra-State supplies to registered persons' as description,

                SUM((CASE WHEN trans_main.tax_class = 'SALESCTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as nill_rate_amount, 

                SUM((CASE WHEN trans_main.tax_class = 'SALESCEXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as exe_amount,

                SUM((CASE WHEN trans_main.tax_class = 'SALESCNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as non_gst_amount

                FROM trans_child
                LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
                WHERE trans_child.is_delete = 0
                AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'                
                AND trans_main.gstin != 'URP'
                AND trans_main.trans_status != 3
                

                UNION ALL 

                SELECT 'Inter-State supplies to unregistered persons' as description,

                SUM((CASE WHEN trans_main.tax_class = 'SALESTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as nill_rate_amount, 

                SUM((CASE WHEN trans_main.tax_class = 'SALESEXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as exe_amount,

                SUM((CASE WHEN trans_main.tax_class = 'SALESNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as non_gst_amount

                FROM trans_child
                LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
                LEFT JOIN party_master ON party_master.id = trans_main.party_id
                LEFT JOIN states on party_master.state_id = states.id
                WHERE trans_child.is_delete = 0
                AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
                AND trans_main.gstin = 'URP'
                AND trans_main.trans_status != 3
                
                
                UNION ALL

                SELECT 'Intra-State supplies to unregistered persons' as description,

                SUM((CASE WHEN trans_main.tax_class = 'SALESCTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as nill_rate_amount, 

                SUM((CASE WHEN trans_main.tax_class = 'SALESCEXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as exe_amount,

                SUM((CASE WHEN trans_main.tax_class = 'SALESCNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Sale','GInc') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type = 'Increase Sales' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END)) as non_gst_amount
                
                FROM trans_child
                LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
                LEFT JOIN party_master ON party_master.id = trans_main.party_id
                LEFT JOIN states on party_master.state_id = states.id
                WHERE trans_child.is_delete = 0
                AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
                AND trans_main.gstin = 'URP'
                AND trans_main.trans_status != 3
                
            ";
        else:
            $sql = "
            SELECT 'Inter-State supplies' as description,

            SUM(CASE WHEN party_master.registration_type = 2 THEN (CASE WHEN trans_main.tax_class = 'PURURDGSTACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as compo_amount, 

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PURTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as nill_rate_amount, 

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PUREXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as exe_amount,

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PURNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as non_gst_amount

            FROM trans_child
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN states on party_master.state_id = states.id
            WHERE trans_child.is_delete = 0
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.trans_status != 3
            
            
            UNION ALL

            SELECT 'Intra-State supplies' as description,

            SUM(CASE WHEN party_master.registration_type = 2 THEN (CASE WHEN trans_main.tax_class = 'PURURDIGSTACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as compo_amount, 

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PURCTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as nill_rate_amount, 

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PURCEXEMPTEDTFACC' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as exe_amount,

            SUM(CASE WHEN party_master.registration_type != 2 THEN (CASE WHEN trans_main.tax_class = 'PURCNONGST' THEN (CASE WHEN trans_main.vou_name_s IN ('Purc','GExp') THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'C.N.' AND trans_main.order_type = 'Increase Purchase' THEN trans_child.taxable_amount WHEN trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Decrease Purchase','Purchase Return') THEN (trans_child.taxable_amount * -1) ELSE 0 END) ELSE 0 END) ELSE 0 END) as non_gst_amount

            FROM trans_child
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN states on party_master.state_id = states.id
            WHERE trans_child.is_delete = 0
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.trans_status != 3
            
            ";
        endif;

        $result = $this->db->query($sql)->result();
        
        return $result;
    }

    public function _hsn($data){
        //trans_main.order_type IN ('Increase Sales','Decrease Sales','Sales Return')
        //trans_main.order_type IN ('Increase Purchase','Decrease Purchase','Purchase Return')
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $decreaseEntryType = ($data['report'] == "gstr1")?'"C.N."':'"D.N."';
        
        $orderTypes = ($data['report'] == "gstr1")?" AND (trans_main.vou_name_s IN ('Sale','GInc') OR (trans_main.vou_name_s IN ('C.N.','D.N.') AND trans_main.order_type IN ('Increase Sales','Decrease Sales','Sales Return')))":" AND (trans_main.vou_name_s IN ('Purc','GExp') OR (trans_main.vou_name_s IN ('C.N.','D.N.') AND trans_main.order_type IN ('Increase Purchase','Decrease Purchase','Purchase Return')))";
        $p_or_m = ($data['report'] == "gstr1")?-1:1;

        $groupBy = ($data['report'] == "gstr1")?" GROUP BY trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per":" GROUP BY trans_child.hsn_code,trans_child.unit_id";

        $result = $this->db->query("
            SELECT trans_main.entry_type,trans_child.hsn_code,hsn_master.description as hsn_description,trans_child.gst_per,unit_master.unit_name,unit_master.description as unit_description,

            SUM((trans_child.qty * trans_child.p_or_m * $p_or_m)) as qty,

            SUM((trans_child.taxable_amount * trans_child.p_or_m * $p_or_m)) as taxable_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.cgst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as cgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.sgst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as sgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 2 THEN (trans_child.igst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as igst_amount,
            SUM((trans_child.cess_amount * trans_child.p_or_m * $p_or_m)) as cess_amount,
            SUM((trans_child.net_amount * trans_child.p_or_m * $p_or_m)) as net_amount
            
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN unit_master ON unit_master.id=trans_child.unit_id
            LEFT JOIN hsn_master ON hsn_master.hsn = trans_child.hsn_code
            WHERE trans_child.is_delete = 0
            ".$orderTypes."
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.trans_status != 3
            ".$party_id."
            
            $groupBy
        ")->result();
        
        return $result;
    }
	
    public function _docs($data){

        $party_id = (!empty($data['party_id']))?" AND trans_main.party_id = ".$data['party_id']:"";
        

        $result = $this->db->query("
            SELECT  (CASE WHEN vou_name_s IN ('Sale','GInc') THEN 'Invoices for outward supply' ELSE trans_main.vou_name_l END) as document,MAX(trans_main.trans_no) as max_trans_no, MIN(trans_main.trans_no) as min_trans_no, SUM(CASE WHEN trans_status <> 3 THEN 1 ELSE 0 END) as total_inv, trans_main.trans_number,trans_main.trans_prefix,SUM(CASE WHEN trans_status = 3 THEN 1 ELSE 0 END) as total_cnl_inv
            
            FROM trans_main 
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            WHERE trans_main.is_delete = 0
            AND ( trans_main.vou_name_s IN ('Sale','GInc') OR (trans_main.vou_name_s = 'C.N.' AND trans_main.order_type IN ('Decrease Sales','Sales Return') ) OR (trans_main.vou_name_s = 'D.N.' AND trans_main.order_type IN ('Increase Sales') ) )
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            ".$party_id."
            
            GROUP BY trans_main.vou_name_s,trans_main.trans_prefix
        ")->result();
        return $result;
    }

    public function _imps($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.gstin,trans_main.tax_class,trans_main.port_code,trans_main.ship_bill_no,trans_main.ship_bill_date,trans_main.sales_type,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,trans_main.itc,states.name as state_name,trans_main.party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.tax_class IN ('IMPORTSACC')
            AND trans_main.trans_status != 3
            ".$party_id."
            
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _impg($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.gstin,trans_main.tax_class,trans_main.port_code,trans_main.ship_bill_no,trans_main.ship_bill_date,trans_main.sales_type,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,trans_main.itc,states.name as state_name,trans_main.party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.tax_class IN ('IMPORTACC','SEZRACC')
            AND trans_main.trans_status != 3
            ".$party_id."
            
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _itcr($data){
        return array();
    }

    public function _taxableSummary($data){
        $queryData = array();
        $queryData['tableName'] = "trans_child";
        $queryData['select'] = "SUM(trans_child.taxable_amount * trans_main.p_or_m) as taxable_amount, SUM(CASE WHEN trans_main.gst_type = 2 THEN (trans_child.igst_amount * trans_main.p_or_m) ELSE 0 END) as igst_amount, SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.cgst_amount * trans_main.p_or_m) ELSE 0 END) as cgst_amount, SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.sgst_amount * trans_main.p_or_m) ELSE 0 END) as sgst_amount, SUM(trans_child.cess_amount * trans_main.p_or_m) as cess_amount";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        $queryData['where_in']['trans_main.vou_name_s'] = $data['vou_name_s'];
        $queryData['where']['trans_main.trans_status !='] = 3;
        $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
        $queryData['where']['trans_main.trans_date <='] = $data['to_date'];
        
        if(isset($data['registered'])):
            if($data['registered'] == 1):
                $queryData['where']['trans_main.gstin !='] = "URP";
            elseif($data['registered'] == 0):
                $queryData['where']['trans_main.gstin'] = "URP";
            endif;
        endif;

        if(!empty($data['customCondition'])):
            $queryData['customWhere'][] = $data['customCondition'];
        endif;

        if(!empty($data['order_type'])):
            $queryData['where_in']['trans_main.order_type'] = $data['order_type'];
        endif;
        
        if(!empty($data['tax_class']))
            $queryData['where_in']['trans_main.tax_class'] = $data['tax_class'];

        if(!empty($data['not_tax_class']))
            $queryData['where_not_in']['trans_main.tax_class'] = $data['not_tax_class'];

        if(!empty($data['itc']))
            $queryData['where_in']['trans_main.itc'] = $data['itc'];

        
        $result = $this->row($queryData);
        //$this->printQuery();
        return $result;
    }

    public function _stateWiseURSummary($data){
        $companyInfo = $this->getCompanyInfo();

        $queryData = array();
        $queryData['tableName'] = "trans_child";
        $queryData['select'] = "SUM(trans_child.taxable_amount * trans_main.p_or_m) as taxable_amount, SUM(CASE WHEN trans_main.gst_type = 2 THEN (trans_child.igst_amount * trans_main.p_or_m) ELSE 0 END) as igst_amount, SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.cgst_amount * trans_main.p_or_m) ELSE 0 END) as cgst_amount, SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.sgst_amount * trans_main.p_or_m) ELSE 0 END) as sgst_amount, SUM(trans_child.cess_amount * trans_main.p_or_m) as cess_amount,states.name as state_name,trans_main.party_state_code";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['states'] = "trans_main.party_state_code = states.gst_statecode";

        $queryData['where_in']['trans_main.vou_name_s'] = $data['vou_name_s'];
        $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
        $queryData['where']['trans_main.trans_date <='] = $data['to_date'];
        
        $queryData['where']['trans_main.party_state_code !='] = $companyInfo->company_state_code;

        $queryData['where']['trans_main.gstin'] = "URP";
        $queryData['where']['trans_main.trans_status !='] = 3;
        $queryData['where_not_in']['trans_main.tax_class'] = ["'IMPORTACC'","'IMPORTSACC'","'SEZRACC'","'EXPORTGSTACC'","'EXPORTTFACC'"];

        $queryData['group_by'][] = "trans_main.party_state_code,states.name";

        $result = $this->rows($queryData);
        return $result;
    }
}
?>
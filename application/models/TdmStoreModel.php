<?php
class TdmStoreModel extends MasterModel{
    
    private $transMain = "trans_main"; 
    
    public function sendSMS($mobiles,$message){
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://sms.scubeerp.in/sendSMS?");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "username=9427235336&message=".$message."&sendername=NTVBIT&smstype=TRANS&numbers=".$mobiles."&apikey=7d37fc6d-a141-4f81-9d79-159cf37c3342");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
	}
	
	public function getInvoiceData($data){		
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
		
        $queryData['select'] = "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=4 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si4,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=5 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si5,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=6 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si6,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=7 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si7,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=8 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si8,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=9 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si9,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=10 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si10,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=11 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si11,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=12 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as si12,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=1 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si1,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=2 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si2,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['si_entry_type']." AND MONTH(trans_date)=3 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as si3,";
		
		$queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=4 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi4,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=5 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi5,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=6 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi6,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=7 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi7,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=8 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi8,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=9 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi9,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=10 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi10,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=11 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi11,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=12 AND YEAR(trans_date)=".$this->startYear." THEN taxable_amount ELSE 0 END) as pi12,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=1 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi1,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=2 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi2,";
        $queryData['select'] .= "SUM(CASE WHEN entry_type=".$data['pi_entry_type']." AND MONTH(trans_date)=3 AND YEAR(trans_date)=".$this->endYear." THEN taxable_amount ELSE 0 END) as pi3";
		
        $queryData['where_in']['entry_type'] = [$data['si_entry_type'],$data['pi_entry_type']];
		$result = $this->row($queryData);
		return $result;
    }
}
?>
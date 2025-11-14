<?php
class CountriesModel extends MasterModel
{
    private $countries = "countries";
    private $states = "states";
    private $cities = "cities";

    public function getDTRows($data){
        $data['tableName'] = $this->countries;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "countries.name";

        $columns = array();
        foreach ($data['searchCol'] as $row): $columns[] = $row;
        endforeach;

        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }

        return $this->pagingRows($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['name'] = "countries is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->countries, $data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->countries;

        if(!empty($data['name']))
            $queryData['where']['name'] = $data['name'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getContries($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->countries;
        return $this->row($queryData);
    }

    public function delete($id){
        try {
            $this->db->trans_begin();

            $result = $this->trash($this->countries, ['id' => $id], 'Countries');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    //state
    public function getStatesDTRows($data){
        $data['tableName'] = $this->states;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "states.name";

        $columns = array();
        foreach ($data['searchCol'] as $row): $columns[] = $row;
        endforeach;

        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }

        return $this->pagingRows($data);
    }
    
	public function saveStae($data){
        try {
            $this->db->trans_begin();

            if($this->checkStateDuplicate($data) > 0):
                $errorMessage['name'] = "State is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->states, $data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkStateDuplicate($data){
        $queryData['tableName'] = $this->states;

        if(!empty($data['name']))
            $queryData['where']['name'] = $data['name'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getState($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->states;
        return $this->row($queryData);
    }
    
	public function deleteState($id){
        try {
            $this->db->trans_begin();

            $result = $this->trash($this->states, ['id' => $id], 'States');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    //cities
    public function getCitiesDTRows($data){
        $data['tableName'] = $this->cities;
		$data['select'] = "cities.*,states.name as state_name,countries.name as country_name";
		$data['leftJoin']['countries'] = 'countries.id = cities.country_id';
		$data['leftJoin']['states'] = 'states.id = cities.state_id';
		$data['order_by']['countries.name'] = 'ASC';
		$data['order_by']['states.name'] = 'ASC';
		$data['order_by']['cities.name'] = 'ASC';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "countries.name";
        $data['searchCol'][] = "states.name";
        $data['searchCol'][] = "cities.name";

        $columns = array();

        foreach ($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }

        return $this->pagingRows($data);
    }

    public function saveCities($data){
        try {
            $this->db->trans_begin();

            if($this->checkCitiesDuplicate($data) > 0):
                $errorMessage['name'] = "Cities is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->cities, $data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkCitiesDuplicate($data){
        $queryData['tableName'] = $this->cities;

        if(!empty($data['name']))
            $queryData['where']['name'] = $data['name'];
        if(!empty($data['country_id']))
            $queryData['where']['country_id'] = $data['country_id'];
        if(!empty($data['state_id']))
            $queryData['where']['state_id'] = $data['state_id'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function editCities($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->cities;
        return $this->row($queryData);
    }

    public function deleteCities($id){
        try {
            $this->db->trans_begin();

            $result = $this->trash($this->cities, ['id' => $id], 'Cities');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
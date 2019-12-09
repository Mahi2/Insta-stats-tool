<?php

/* Helper class for generating and answering to DataTables */
class DataTable {
    public $accepted_columns = [];
    public $accepted_order_directions = ['asc', 'desc'];
    public $response;

    public function set_accepted_columns(Array $columns) {
        $this->accepted_columns = $columns;
    }

    public function get_columns() {
        return $this->response->columns;
    }

    public function get_order() {
        return $this->response->order;
    }

    public function get_search() {
        return $this->response->search;
    }

    public function get_draw() {
        return $this->response->draw;
    }

    public function get_start() {
        return $this->response->start;
    }

    public function get_length() {
        return $this->response->length;
    }

    public function process($data) {

        $this->response = new StdClass();

        $this->response->columns = $this->columns_query($data['columns']);
        $this->response->order = $this->order_query($data['order'], $data['columns']);

        $this->response->search = Database::clean_string($_POST['search']['value']);
        $this->response->draw = (int) filter_var($_POST['draw'], FILTER_SANITIZE_NUMBER_INT);
        $this->response->start = (int) filter_var($_POST['start'], FILTER_SANITIZE_NUMBER_INT);
        $this->response->length = (int) filter_var($_POST['length'], FILTER_SANITIZE_NUMBER_INT);
    }

    public function columns_query($columns) {
        $columns_array = [];

        foreach($columns as $column) {
            $column_name = Database::clean_string($column['data']);

            if(in_array($column_name, $this->accepted_columns)) {
                $columns_array[] = $column_name;
            }
        }

        $string = implode(', ', $columns_array);

        return $string;
    }

    public function order_query($order, $columns) {
        $order_array = [];

        foreach($order as $entry) {

            $column_name = Database::clean_string($columns[$entry['column']]['data']);
            $direction = Database::clean_string($entry['dir']);

            if(in_array($column_name, $this->accepted_columns) && in_array($direction, $this->accepted_order_directions)) {
                $order_array[] = $column_name . ' ' . $direction;
            }
        }

        $string = implode(', ', $order_array);

        return $string;
    }
}

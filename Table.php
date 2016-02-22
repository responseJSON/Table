<?php
/*
 * Creator: Mochamad Rohmat
 * Email: rohmat.moch@gmail.com
 * Website: cahbiyen.blogspot.com
 * lisence: Cahbiyen Inc
 * Version: 1.0.0
 * ==============================
 * if any questiont please send email to me
 */
class Table {

	/*
	 * Set properties
	 * ====================
	 * @public
	 * $empty default null
	 * 
	 * @private
	 * $template default array
	 * $type default heading
	 * $table default null
	 * $heading default array
	 * $body default array
	 * $footer default array
	 * $rows default array
	 */
	public $empty = null;

	private $template = array();
	private $type = 'heading';
	private $table = null;
	private $heading = array();
	private $body = array();
	private $footer = array();
	private $rows = array();

	/*
	 * __construct
	 * if you want change template table you must set template in __construct($templates)
	 * default element table is <table border="1">..</table>
	 * example:
	 * $table = new Table(array('table_open'=>'<table class="table">');
	 * echo $table->build()
	 * 
	 * result
	 * <table class="table">...</table>
	 */
	public function __construct($template = array()) {

		/*
		 * change array to JSON
		 * and change JSON to Array Object
		 */
		$this->template = json_encode($template);
		$this->template = json_decode($this->template);
	}

	/*
	 * generate table
	 */
	public function build() {
		$table = $this->set_template()->table_open;

		/*
		 * if heading exist, create heading in table
		 */
		if (count($this->heading) > 0) {
			$table .= $this->set_template()->thead_open;
			$table .= $this->get_row($this->heading);
			$table .= $this->set_template()->thead_close;
		}

		/*
		 * if body exist, create body in table
		 */
		if (count($this->body) > 0) {
			$table .= $this->set_template()->tbody_open;
			$table .= $this->get_row($this->body);
			$table .= $this->set_template()->tbody_close;
		}

		/*
		 * if footer exist, create footer in table
		 */
		if (count($this->footer) > 0) {
			$table .= $this->set_template()->tfoot_open;
			$table .= $this->get_row($this->footer);
			$table .= $this->set_template()->tfoot_close;
		}

		$table .= $this->set_template()->table_close;

		return $table;
	}

	/*
	 * if you want make 2 table you must set clear() after build() table
	 * example
	 * $table = new Table();
	 * // table 1
	 * $table->set_row(array('ID 1','NAME 1','ADDRESS 1'));
	 * echo $table->build();
	 * $table->clear();
	 *
	 * // table 2
	 * $table->set_row(array('ID 2','NAME 2','ADDRESS 2'));
	 * echo $table->build();
	 */
	public function clear() {
		$sturctures = array('heading', 'body', 'footer');
		foreach ($sturctures as $sturcture) {
			$this->$sturcture = array();
		}
	}

	/*
	 * in set_row you have 3 type rows : heading, body, and footer
	 * $row default array
	 * $type default heading
	 *
	 * you can set function in 3 types
	 * 1. set_row('ID, NAME, ADDRESS');
	 *    result : <tr><th>NAME<th>...</tr>
	 * 2. set_row(array('ID','NAME','ADDRESS'));
	 *    result : <tr><th>NAME<th>...</tr>
	 * 3. set_row(array(array('content'=>'ID', 'align'=>'center'), array('content'=>'NAME', 'align'=>'center'), array('content'=>'ADDRESS', 'align'=>'center')));
	 *    result : <tr><th align="center">NAME<th>...</tr>
	 */
	public function set_row($rows = array(), $type = 'heading') {
		$rows = is_array($rows) ? $rows : explode(',', $rows);
		$this->rows = array('datas'=>$rows, 'type'=>$type);

		switch ($type) {
			case 'heading': array_push($this->heading, $this->rows); break;
			case 'body': array_push($this->body, $this->rows); break;
			case 'footer': array_push($this->footer, $this->rows); break;
			default : array_push($this->heading, $this->rows); break;
		}
	}

	/*
	 * get_row will generate heading, body or footer
	 * after you set_row function
	 */
	private function get_row($rows) {
		$head = null;

		foreach ($rows as $key=>$val) {

			/*
			 * check type rows
			 */
			switch ($val['type']) {
				case 'heading': $element = 'th'; break;
				case 'body': $element = 'td'; break;
				case 'footer': $element = 'th'; break;
				default : $element = 'th'; break;
			}

			$head .= $this->set_template()->tr_open;
			foreach ($val['datas'] as $k=>$v) {
				if (is_array($v)) {
					$head .= '<'.$element;
					$head .= $this->attributes($v['attributes']).'>';
					$head .= !empty($v['content']) ? $v['content'] : $this->empty;
					$head .= '</'.$element.'>';
				} else {
					$head .= '<'.$element.'>';
					$head .= !empty($v) ? $v : $this->empty;
					$head .= '</'.$element.'>';
				}
			}
			$head .= $this->set_template()->tr_close;
		}

		return $head;
	}

	/*
	 * will make attribute in rows data
	 */
	private function attributes($attributes) {
		$attr = null;
		if (is_array($attributes)) {
			foreach ($attributes as $key=>$val) {
				$attr .= ' '.$key.'="'.$val.'"';
			}
		} else {
			$attr .= ' '.$attributes;
		}

		return $attr;
	}

	/*
	 * set template table
	 */
	private function set_template() {
		$table_open = $this->check_template(@$this->template->table_open, '<table border="1">');
		$table_close = $this->check_template(@$this->template->table_close, '</table>');
		$thead_open = $this->check_template(@$this->template->thead_open, '<thead>');
		$thead_close = $this->check_template(@$this->template->thead_close, '</thead>');
		$tbody_open = $this->check_template(@$this->template->tbody_open, '<tbody>');
		$tbody_close = $this->check_template(@$this->template->tbody_close, '</tbody>');
		$tfoot_open = $this->check_template(@$this->template->tfoot_open, '<tfoot>');
		$tfoot_close = $this->check_template(@$this->template->tfoot_close, '</tfoot>');
		$tr_open = $this->check_template(@$this->template->tr_open, '<tr>');
		$tr_close = $this->check_template(@$this->template->tr_close, '</tr>');

		$table_structure = array(
			'table_open'=>$table_open,
			'table_close'=>$table_close,
			'thead_open'=>$thead_open,
			'thead_close'=>$thead_close,
			'tbody_open'=>$tbody_open,
			'tbody_close'=>$tbody_close,
			'tfoot_open'=>$tfoot_open,
			'tfoot_close'=>$tfoot_close,
			'tr_open'=>$tr_open,
			'tr_close'=>$tr_close
		);
		$table_structure = json_encode($table_structure);
		$table_structure = json_decode($table_structure);

		return $table_structure;
	}

	/*
	 * check template table
	 */
	private function check_template($template, $change) {
		return !empty($template) ? $template : $change;
	}
}

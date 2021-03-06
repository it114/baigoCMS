<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

include_once(BG_PATH_CLASS . "tpl_admin.class.php"); //载入模板类
include_once(BG_PATH_MODEL . "tag.class.php");

/*-------------允许类-------------*/
class CONTROL_TAG {

	public $obj_tpl;
	public $mdl_tag;
	public $adminLogged;

	function __construct() { //构造函数
		$this->obj_base       = $GLOBALS["obj_base"];
		$this->config         = $this->obj_base->config;
		$this->adminLogged    = $GLOBALS["adminLogged"];
		$this->mdl_tag        = new MODEL_TAG(); //设置上传信息对象
		$this->obj_tpl        = new CLASS_TPL(BG_PATH_SYSTPL_ADMIN . $this->config["ui"]); //初始化视图对象
		$this->tplData = array(
			"adminLogged" => $this->adminLogged
		);
	}


	/**
	 * ctl_list function.
	 *
	 * @access public
	 * @return void
	 */
	function ctl_list() {
		if (!isset($this->adminLogged["groupRow"]["group_allow"]["article"]["tag"])) {
			return array(
				"str_alert" => "x130301",
			);
			exit;
		}

		$_act_get     = fn_getSafe($GLOBALS["act_get"], "txt", "");
		$_str_key     = fn_getSafe(fn_get("key"), "txt", "");
		$_str_status  = fn_getSafe(fn_get("status"), "txt", "");
		$_num_tagId   = fn_getSafe(fn_get("tag_id"), "int", 0);

		$_arr_search = array(
			"act_get"    => $_act_get,
			"key"        => $_str_key,
			"status"     => $_str_status,
		);

		$_num_tagCount    = $this->mdl_tag->mdl_count($_str_key, $_str_status);
		$_arr_page        = fn_page($_num_tagCount); //取得分页数据
		$_str_query       = http_build_query($_arr_search);
		$_arr_tagRows     = $this->mdl_tag->mdl_list(BG_DEFAULT_PERPAGE, $_arr_page["except"], $_str_key, $_str_status);

		if ($_num_tagId > 0) {
			$_arr_tagRow = $this->mdl_tag->mdl_read($_num_tagId);
			if ($_arr_tagRow["str_alert"] != "y130102") {
				return $_arr_tagRow;
				exit;
			}
		} else {
			$_arr_tagRow = array(
				"tag_id"        => 0,
				"tag_name"      => "",
				"tag_status"    => "show",
			);
		}

		$_arr_tpl = array(
			"query"      => $_str_query,
			"pageRow"    => $_arr_page,
			"search"     => $_arr_search,
			"tagRow"     => $_arr_tagRow,
			"tagRows"    => $_arr_tagRows,
		);

		$_arr_tplData = array_merge($this->tplData, $_arr_tpl);

		$this->obj_tpl->tplDisplay("tag_list.tpl", $_arr_tplData);

		return array(
			"str_alert" => "y130301",
		);
	}

}

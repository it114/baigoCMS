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
include_once(BG_PATH_MODEL . "article.class.php"); //载入文章模型类
include_once(BG_PATH_MODEL . "cate.class.php");
include_once(BG_PATH_MODEL . "cateBelong.class.php");
include_once(BG_PATH_MODEL . "tag.class.php");
include_once(BG_PATH_MODEL . "mark.class.php");
include_once(BG_PATH_MODEL . "spec.class.php");
include_once(BG_PATH_MODEL . "custom.class.php");

/*-------------文章控制器-------------*/
class CONTROL_ARTICLE {

	private $obj_base;
	private $config;
	private $adminLogged;
	private $obj_tpl;
	private $mdl_article;
	private $mdl_cate;
	private $mdl_cateBelong;
	private $mdl_mark;
	private $mdl_admin;

	function __construct() { //构造函数
		$this->obj_base       = $GLOBALS["obj_base"];
		$this->config         = $this->obj_base->config;
		$this->adminLogged    = $GLOBALS["adminLogged"]; //获取已登录信息
		$this->obj_tpl        = new CLASS_TPL(BG_PATH_SYSTPL_ADMIN . $this->config["ui"]); //初始化视图对象
		$this->mdl_article    = new MODEL_ARTICLE(); //设置文章对象
		$this->mdl_cate       = new MODEL_CATE(); //设置栏目对象
		$this->mdl_cateBelong = new MODEL_CATE_BELONG(); //设置栏目从属对象
		$this->mdl_tag        = new MODEL_TAG();
		$this->mdl_mark       = new MODEL_MARK(); //设置标记对象
		$this->mdl_spec       = new MODEL_SPEC();
		$this->mdl_admin      = new MODEL_ADMIN(); //设置管理员对象
		$this->mdl_custom     = new MODEL_CUSTOM();
		$this->tplData = array(
			"adminLogged" => $this->adminLogged
		);
	}


	/** 文章表单
	 * ctl_form function.
	 *
	 * @access public
	 * @return void
	 */
	function ctl_form() {
		$_num_articleId = fn_getSafe(fn_get("article_id"), "int", 0);

		if ($_num_articleId > 0) {
			$_arr_articleRow = $this->mdl_article->mdl_read($_num_articleId); //读取文章
			if ($_arr_articleRow["str_alert"] != "y120102") {
				return $_arr_articleRow;
				exit;
			}

			if (!isset($this->adminLogged["groupRow"]["group_allow"]["article"]["edit"]) && !isset($this->adminLogged["admin_allow_cate"][$_arr_articleRow["article_cate_id"]]["edit"]) && $_arr_articleRow["article_admin_id"] != $this->adminLogged["admin_id"]) { //判断权限
				return array(
					"str_alert" => "x120303"
				);
				exit;
			}

			$_arr_cateBelongRows = $this->mdl_cateBelong->mdl_list($_arr_articleRow["article_id"]); //读取从属数据
			foreach ($_arr_cateBelongRows as $_value) {
				$_arr_articleRow["cate_ids"][] = $_value["belong_cate_id"];
			}

			$_arr_articleRow["cate_ids"][]   = $_arr_articleRow["article_cate_id"];
			$_arr_tagRows                    = $this->mdl_tag->mdl_list(10, 0, "", "show", "tag_id", $_arr_articleRow["article_id"]); //读取从属数据

			$_arr_articleRow["article_tags"] = array();

			foreach ($_arr_tagRows as $_value) {
					$_arr_articleRow["article_tags"][]  = $_value["tag_name"];
			}
			$_arr_articleRow["article_tags"] = json_encode($_arr_articleRow["article_tags"]);

			$_arr_specRow = $this->mdl_spec->mdl_read($_arr_articleRow["article_spec_id"]);

			$_arr_articleRow["article_excerpt_type"] = "manual";

			//print_r($_arr_articleRow);
		} else {
			if (isset($this->adminLogged["groupRow"]["group_allow"]["article"]["approve"])) {
				$_str_status = "pub";
			} else {
				$_str_status = "wait";
			}
			$_arr_articleRow = array(
				"article_id"            => 0,
				"article_title"         => "",
				"article_content"       => "",
				"article_link"          => "",
				"article_excerpt"       => "",
				"article_excerpt_type"  => "auto",
				"article_cate_id"       => 0,
				"article_status"        => $_str_status,
				"article_box"           => "normal",
				"article_time_pub"      => time(),
				"cate_ids"              => array(),
				"article_tags"          => array(),
			);
			$_arr_specRow = array();
		}

		//print_r($_arr_articleRow);

		$_arr_cateRows    = $this->mdl_cate->mdl_list(1000, 0, "show");
		$_arr_markRows    = $this->mdl_mark->mdl_list(100);
		$_arr_customRows  = $this->mdl_custom->mdl_list(100, 0, "", "article", "enable");
		//print_r($_arr_customRows);

		if (count($_arr_cateRows) < 1) {
			return array(
				"str_alert" => "x110401",
			);
			exit;
		}

		$_arr_articleRow["cate_ids"] = array_unique($_arr_articleRow["cate_ids"]);

		$_arr_tpl = array(
			"specRow"    => $_arr_specRow,
			"cateRows"   => $_arr_cateRows, //栏目列表
			"markRows"   => $_arr_markRows, //标记列表
			"customRows" => $_arr_customRows, //标记列表
			"articleRow" => $_arr_articleRow, //栏目信息
		);

		$_arr_tplData = array_merge($this->tplData, $_arr_tpl);

		$this->obj_tpl->tplDisplay("article_form.tpl", $_arr_tplData);

		return array(
			"str_alert" => "y120102"
		);
	}


	/** 显示文章
	 * ctl_show function.
	 *
	 * @access public
	 * @param mixed $num_articleId
	 * @param int $num_ucId (default: 0)
	 * @return void
	 */
	function ctl_show() {
		$_num_articleId = fn_getSafe(fn_get("article_id"), "int", 0);
		if ($_num_articleId == 0) {
			return array(
				"str_alert" => "x120212"
			);
			exit;
		}

		$_arr_articleRow = $this->mdl_article->mdl_read($_num_articleId); //读取文章
		if ($_arr_articleRow["str_alert"] != "y120102") {
			return $_arr_articleRow;
			exit;
		}

		if (!isset($this->adminLogged["groupRow"]["group_allow"]["article"]["browse"]) && !isset($this->adminLogged["admin_allow_cate"][$_arr_articleRow["article_cate_id"]]["browse"]) && $_arr_articleRow["article_admin_id"] != $this->adminLogged["admin_id"]) { //判断权限
			return array(
				"str_alert" => "x120301"
			);
			exit;
		}

		$_arr_belongRow = $this->mdl_cateBelong->mdl_list($_arr_articleRow["article_id"]);
		foreach ($_arr_belongRow as $_value) {
			$_arr_articleRow["cate_ids"][] = $_value["belong_cate_id"];
		}
		$_arr_articleRow["cate_ids"][]    = $_arr_articleRow["article_cate_id"];

		$_arr_cateRow     = $this->mdl_cate->mdl_read($_arr_articleRow["article_cate_id"]);
		$_arr_cateRows    = $this->mdl_cate->mdl_list(1000, 0, "show");
		$_arr_markRow     = $this->mdl_mark->mdl_read($_arr_articleRow["article_mark_id"]);
		$_arr_tagRows     = $this->mdl_tag->mdl_list(10, 0, "", "", "tag_id", $_arr_articleRow["article_id"]); //读取从属数据
		$_arr_customRows  = $this->mdl_custom->mdl_list(100, 0, "", "article", "enable");

		$_arr_articleRow["cate_ids"]  = array_unique($_arr_articleRow["cate_ids"]);

		$_arr_tpl = array(
			"cateRow"    => $_arr_cateRow, //栏目
			"cateRows"   => $_arr_cateRows, //栏目列表
			"markRow"    => $_arr_markRow, //标记列表
			"customRows" => $_arr_customRows, //标记列表
			"articleRow" => $_arr_articleRow,
			"tagRows"    => $_arr_tagRows,
		);

		$_arr_tplData = array_merge($this->tplData, $_arr_tpl);

		$this->obj_tpl->tplDisplay("article_show.tpl", $_arr_tplData);

		return array(
			"str_alert" => "y120102"
		);
	}


	/** 列出文章
	 * ctl_list function.
	 *
	 * @access public
	 * @return void
	 */
	function ctl_list() {
		$_act_get     = fn_getSafe($GLOBALS["act_get"], "txt", "");
		$_str_key     = fn_getSafe(fn_get("key"), "txt", "");
		$_str_year    = fn_getSafe(fn_get("year"), "txt", "");
		$_str_month   = fn_getSafe(fn_get("month"), "txt", "");
		$_str_status  = fn_getSafe(fn_get("status"), "txt", "");
		$_str_box     = fn_getSafe(fn_get("box"), "txt", "");
		$_num_cateId  = fn_getSafe(fn_get("cate_id"), "int", 0);
		$_num_markId  = fn_getSafe(fn_get("mark_id"), "int", 0);
		$_num_adminId = fn_getSafe(fn_get("admin_id"), "int", 0);

		$_arr_search = array(
			"act_get"    => $_act_get,
			"key"        => $_str_key,
			"year"       => $_str_year,
			"month"      => $_str_month,
			"status"     => $_str_status,
			"box"        => $_str_box,
			"cate_id"    => $_num_cateId,
			"mark_id"    => $_num_markId,
			"admin_id"   => $_num_adminId,
		);

		if ($_num_cateId != 0) {
			$_arr_cateIds    = $this->mdl_cate->mdl_cateIds($_num_cateId);
			$_arr_cateIds[]  = $_num_cateId;
			$_arr_cateIds    = array_unique($_arr_cateIds);
		} else {
			$_arr_cateIds = false;
		}

		if ($_arr_search["admin_id"] == 0) {
			if (isset($this->adminLogged["groupRow"]["group_allow"]["article"]["del"])) {
				$_num_adminId = 0;
			} else {
				$_num_adminId = $this->adminLogged["admin_id"];
			}
		}

		if ($_str_box == "draft" || $_str_box == "recycle") {
			$_num_adminId = $this->adminLogged["admin_id"];
		}

		$_num_articleCount            = $this->mdl_article->mdl_count($_str_key, $_str_year, $_str_month, $_str_status, $_str_box, $_arr_cateIds, $_num_markId, 0, $_num_adminId);
		$_arr_page                    = fn_page($_num_articleCount); //取得分页数据
		$_str_query                   = http_build_query($_arr_search);
		$_arr_articleRows             = $this->mdl_article->mdl_list(BG_DEFAULT_PERPAGE, $_arr_page["except"], $_str_key, $_str_year, $_str_month, $_str_status, $_str_box, $_arr_cateIds, $_num_markId, 0, $_num_adminId);

		$_arr_articleCount["all"]     = $this->mdl_article->mdl_count();
		$_arr_articleCount["draft"]   = $this->mdl_article->mdl_count("", "", "", "", "draft", false, 0, $this->adminLogged["admin_id"]);
		$_arr_articleCount["recycle"] = $this->mdl_article->mdl_count("", "", "", "", "recycle", false, 0, $this->adminLogged["admin_id"]);

		$_arr_articleYear             = $this->mdl_article->mdl_year();
		$_arr_cateRows                = $this->mdl_cate->mdl_list(1000, 0, "show");
		$_arr_markRows                = $this->mdl_mark->mdl_list(100);

		foreach ($_arr_articleRows as $_key=>$_value) {
			$_arr_cateRow = $this->mdl_cate->mdl_read($_value["article_cate_id"]);
			if ($_arr_cateRow["str_alert"] != "y110102" && $_value["article_cate_id"] > 0) {
				$_arr_unknownCate[] = $_value["article_id"];
			}
			$_arr_articleRows[$_key]["cateRow"]  = $_arr_cateRow;
			$_arr_articleRows[$_key]["markRow"]  = $this->mdl_mark->mdl_read($_value["article_mark_id"]);
			$_arr_articleRows[$_key]["adminRow"] = $this->mdl_admin->mdl_read($_value["article_admin_id"]);
		}

		if (isset($_arr_unknownCate)) {
			$this->mdl_article->mdl_unknownCate($_arr_unknownCate);
		}

		$_arr_tpl = array(
			"query"          => $_str_query,
			"pageRow"        => $_arr_page,
			"search"         => $_arr_search,
			"cateRows"       => $_arr_cateRows,
			"markRows"       => $_arr_markRows, //标记列表
			"articleRows"    => $_arr_articleRows,
			"articleCount"   => $_arr_articleCount,
			"articleYear"    => $_arr_articleYear,
		);

		$_arr_tplData = array_merge($this->tplData, $_arr_tpl);

		$this->obj_tpl->tplDisplay("article_list.tpl", $_arr_tplData);

		return array(
			"str_alert" => "y120301"
		);
	}
}

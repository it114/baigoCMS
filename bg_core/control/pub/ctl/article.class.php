<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

/*-------------文章类-------------*/
class CONTROL_ARTICLE {

	private $cateRow;
	private $articleRow;
	private $tplData;
	private $obj_tpl;
	private $mdl_cate;
	private $mdl_articlePub;
	private $mdl_tag;
	private $mdl_attach;
	private $config;

	function __construct() { //构造函数
		$this->mdl_cate       = new MODEL_CATE(); //设置文章对象
		$this->mdl_articlePub = new MODEL_ARTICLE_PUB(); //设置文章对象
		$this->mdl_tag        = new MODEL_TAG();
		$this->article_init();
		$this->obj_tpl        = new CLASS_TPL(BG_PATH_TPL_PUB . $this->config["tpl"]); //初始化视图对象
		$this->mdl_attach     = new MODEL_ATTACH(); //设置文章对象
		$this->mdl_thumb      = new MODEL_THUMB(); //设置上传信息对象
	}


	/**
	 * ctl_list function.
	 *
	 * @access public
	 * @return void
	 */
	function ctl_show() {
		if ($this->search["article_id"] == 0) {
			return array(
				"str_alert" => "x120212",
			);
			exit;
		}

		if ($this->articleRow["str_alert"] != "y120102") {
			return $this->articleRow;
			exit;
		}

		if (strlen($this->articleRow["article_title"]) < 1 || $this->articleRow["article_status"] != "pub" || $this->articleRow["article_box"] != "normal" || $this->articleRow["article_time_pub"] > time()) {
			return array(
				"str_alert" => "x120102",
			);
			exit;
		}

		if ($this->articleRow["article_link"]) {
			return array(
				"str_alert" => "x120213",
				"article_link" => $this->articleRow["article_link"],
			);
			exit;
		}

		if ($this->cateRow["str_alert"] != "y110102") {
			return $this->cateRow;
			exit;
		}

		if ($this->cateRow["cate_status"] != "show") {
			return array(
				"str_alert" => "x110102",
			);
			exit;
		}

		if ($this->cateRow["cate_type"] == "link" && $this->cateRow["cate_link"]) {
			return array(
				"str_alert" => "x110218",
				"cate_link" => $this->cateRow["cate_link"],
			);
			exit;
		}

		if ($this->articleRow["article_attach_id"] > 0) {
			if (!file_exists(BG_PATH_CACHE . "thumb_list.php")) {
				$this->mdl_thumb->mdl_cache();
			}
			$_arr_thumbRows = include(BG_PATH_CACHE . "thumb_list.php");
			$this->articleRow["attachRow"]   = $this->mdl_attach->mdl_url($this->articleRow["article_attach_id"], $_arr_thumbRows);
		}

		//print_r(date("W", strtotime("2014-12-01")));

		$this->mdl_articlePub->mdl_hits($this->articleRow["article_id"]);

		//$_arr_tpl = array_merge($this->tplData, $_arr_tplData);

		$this->obj_tpl->tplDisplay("article_show.tpl", $this->tplData);

		return array(
			"str_alert" => "y120102",
		);
	}


	/**
	 * article_init function.
	 *
	 * @access private
	 * @return void
	 */
	private function article_init() {
		$_act_get         = fn_getSafe($GLOBALS["act_get"], "txt", "");
		$_num_articleId   = fn_getSafe(fn_get("article_id"), "int", 0);

		$this->search = array(
			"act_get"    => $_act_get,
			"article_id" => $_num_articleId,
		);

		if(defined("BG_SITE_TPL")) {
			$_str_tpl = BG_SITE_TPL;
		} else {
			$_str_tpl = "default";
		}

		if ($_num_articleId > 0) {
			$this->articleRow = $this->mdl_articlePub->mdl_read($_num_articleId);
			if (!file_exists(BG_PATH_CACHE . "cate_" . $this->articleRow["article_cate_id"] . ".php")) {
				$this->mdl_cate->mdl_cache(array($this->articleRow["article_cate_id"]));
			}

			if ($this->articleRow["str_alert"] == "y120102") {
				$this->cateRow          = include(BG_PATH_CACHE . "cate_" . $this->articleRow["article_cate_id"] . ".php");
				$this->config["tpl"]    = $this->cateRow["cate_tplDo"];
				//$this->config["tpl"]    = $this->mdl_cate->tpl_process($this->articleRow["article_cate_id"]);
			}
		}

		$this->articleRow["cateRow"] = $this->cateRow;

		$this->articleRow["tagRows"] = $this->mdl_tag->mdl_list(10, 0, "", "show", "tag_id", $this->articleRow["article_id"]);

		if (!file_exists(BG_PATH_CACHE . "cate_trees.php")) {
			$this->mdl_cate->mdl_cache();
		}

		$_arr_cateRows = include(BG_PATH_CACHE . "cate_trees.php");

		$this->tplData = array(
			"cateRows"   => $_arr_cateRows,
			"articleRow" => $this->articleRow,
		);
	}
}

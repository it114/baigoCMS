<?php
return "<h3>概述</h3>
	<ol>
		<li>高级部署方式仅适合具备一定 PHP 开发经验，且熟悉网站部署方式的高级用户，初学者不推荐；</li>
		<li>高级部署方式可能需要更高的服务器权限，请确认您拥有足够的权限；</li>
		<li>如采用高级部署方式，建议 SSO 同样采用，详情请查看 baigo SSO 帮助文档中的高级部署。</li>
	</ol>

	<hr>

	<h3>上传文件</h3>

	<p>将下载到的程序包解压，假设服务器的 web 路径为 <mark>/web</mark> 可访问的 web 根目录为 <mark>/web/wwwroot</mark>，对应链接为 <mark>http://www.domain.com</mark> 以下均以此为例。建议按如下方式上传文件：
	<div class=\"panel panel-default\">
		<div class=\"panel-heading\">目录结构说明</div>
		<div class=\"table-responsive\">
			<table class=\"table\">
				<thead>
					<tr>
						<th class=\"nowrap\">文件 / 文件夹</th>
						<th class=\"nowrap\">用途</th>
						<th>建议路径</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class=\"nowrap\">bg_core</td>
						<td class=\"nowrap\">系统内核</td>
						<td>/web</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_cache</td>
						<td class=\"nowrap\">缓存文件</td>
						<td>/web</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_tpl</td>
						<td class=\"nowrap\">模板文件</td>
						<td>/web</td>
					</tr>
					<tr>
						<td class=\"nowrap\">index.php</td>
						<td class=\"nowrap\">前台入口文件</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_admin</td>
						<td class=\"nowrap\">管理后台入口</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_api</td>
						<td class=\"nowrap\">API 接口入口</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_config</td>
						<td class=\"nowrap\">配置文件</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_help</td>
						<td class=\"nowrap\">帮助文档</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_install</td>
						<td class=\"nowrap\">安装程序入口</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_static</td>
						<td class=\"nowrap\">静态文件</td>
						<td>/web/wwwroot</td>
					</tr>
					<tr>
						<td class=\"nowrap\">bg_attach</td>
						<td class=\"nowrap\">附件</td>
						<td>/web/wwwroot</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<hr>

	<h3>修改配置文件</h3>

	<p>在 <mark>/web/wwwroot/bg_confg</mark> 目录下找到 <mark>config.inc.php</mark> 文件，如没有，请将 <mark>config_sample.inc.php</mark> 另存为 <mark>config.inc.php</mark>，找到以下配置项目，建议以如下方式修改：</p>
	<p>
<pre><code class=\"language-php\">define(\"BG_PATH_CORE\", \"/web/bg_core/\");
define(\"BG_PATH_CACHE\", \"/web/bg_cache/\");
define(\"BG_PATH_TPL\", \"/web/bg_tpl/\");</code></pre>
	</p>

	<hr>

	<h3>执行安装程序</h3>

	<p>打开 <mark>http://www.domain.com/bg_install</mark> 按照安装文档的说明进行安装，升级过程类似。详情请查看 <a href=\"{BG_URL_HELP}ctl.php?mod=install\">安装文档</a>。</p>";
./main/css/. #no recursive
dream/up.css.php
vendor/css  #recursvie by default
home/screen.css.sass
test/common.css.manifest.php
/vendor/yui/asset/css
/vendor/test/all.css
//c:\windows\.
//main.css
vendor/css
#//require_css dir
#//require_js dir

<?php
//css.manifest
//可以通过 php 生成 manifest 这样就可以支持按照后缀过滤文件了
//Config::get('');
echo AssetMinifestFileFilter::execute('js', 'js');

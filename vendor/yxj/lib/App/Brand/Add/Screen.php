<?php
namespace Dxl\App\Brand;

class Screen extends \Dxl\View\Screen {
    protected function renderHtmlHeadContent() {
    }

    protected function renderHtmlBodyContent() {
        echo '添加品牌';
        echo '<form method="POST" enctype="multipart/form-data">';
        $value = '';
        if (isset($_POST['name'])) {
            $value = $_POST['name'];
        }
        echo '<div>名称：<input name="name" value="', $value, '"/></div>';
        echo '<div>图标：<input name="logo" type="file" /></div>';
        echo '<div>等级：<input id="r3" checked="checked" name="rank" type="radio" value ="3" /><label for="r3">顶级</label>';
        echo '<div><input id="r2" name="rank" type="radio" value ="2" /><label for="r2">好</label>';
        echo '<div><input id="r1" name="rank" type="radio" value ="1" /><label for="r1">一般</label>';
        echo '</div>';
        if (isset($_POST['location_id'])) {
            $value = $_POST['location_id'];
        }
        echo '<div>发源地 id：<input name="location_id"  value="', $value, '"/></div>';
        if (isset($_POST['parent_id'])) {
            $value = $_POST['parent_id'];
        }
        echo '<div>父品牌 id：<input name="parent_id" value="', $value, '"/></div>';
        echo '<div>分类：<textarea name="category_id_list">';
        if(isset($_POST['category_id_list'])) {
            echo $_POST['category_id_list'];
        } elseif (isset($_GET['category_id'])) {
            echo $_GET['category_id'];
        }
        echo '</textarea></div>';
        echo '<div>摘要：<textarea name="abstract">';
        if(isset($_POST['abstract'])) {
            echo $_POST['abstract'];
        }
        echo '</textarea><div>内容：<textarea name="content">';
        if(isset($_POST['content'])) {
            echo $_POST['content'];
        }
        echo '</textarea></div>';
        $parentId = 0;
        echo '<input type="submit" value="提交" />';
        echo '</form>';
    }
}

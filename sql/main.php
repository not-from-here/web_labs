<?php
include_once 'MyDB.php';
include_once 'connect-inc.php';
include_once 'ProductModel.php';

$path = "http://k503labs.ukrdomen.com/535a/Veprytskaya/images/";
$mysqli = MyDB::get_db_instance();

$p_model = new ProductModel($mysqli);
$sections = $p_model->getSections();

$all_products = null;
$one_product = null;
$current_section = null;
$current_product = null;
$comments = null;

$email = trim(filter_input_('email', ''));
$name = trim(filter_input_('name', ''));
$comment = trim(filter_input_('comment', ''));
$q = filter_input_("section", '');
$id = filter_input_("id", '');

function check()
{
    $f = filter_input_('hidden_input', '');
    if ($f == 'first') {
        return true;
    }
    return false;
}

function get_products($p_model, $section_id)
{
    return $p_model->get_products($section_id);
}

function get_product($p_model, $prod_id)
{
    return array("params" => $p_model->get_product_params($prod_id), "comments" => $p_model->get_product_reviews($prod_id));
}

function set_comment($p_model, $email, $id, $name, $comment)
{
    $p_model->set_product_reviews($email, $id, $name, $comment);
}

function filter_input_($name, $default)
{
    $result = $default;
    if (isset($_POST[$name])) {
        $result = $_POST[$name];
    }
    if (isset($_GET[$name])) {
        $result = $_GET[$name];
    }
    return $result;
}

//section click
if (!empty($q)) {
    $current_section = $q;
    $all_products = get_products($p_model, $current_section);
}
//product click
if (!empty($id)) {
    $current_product = $id;
    $tmp = get_product($p_model, $current_product);
    $one_product = $tmp['params'];
    $comments = $tmp['comments'];
}
//check form submit
if (check()) {
    if (!empty($email) && !empty($name) && !empty($comment) && !empty($id)) {
        set_comment($p_model, $email, $id, $name, $comment);
        $tmp = get_product($p_model, $current_product);
        $one_product = $tmp['params'];
        $comments = $tmp['comments'];
    }
}
?>
<div class="right-col">
    <div class="news-info">
        <a href="">
            <div class="news">
                SQL
            </div>
        </a>
        <div class="date">
            30 февраля 1313
        </div>
    </div>
    <div class="text-content  clearfix">
        <div class="products">
            <?php
            if (is_null($one_product)) {
                foreach ($sections as $key => $value) { ?>
                    <a href="index.php?section=<?= $key ?>" class="buy-item"><?= $value ?></a>
                    <?php
                    if ($current_section == $key) {
                        foreach ($all_products as $k => $v) { ?>
                            <div class="product">
                                <div class="item-name"><?= $v['name'] ?></div>
                                <img src="<?= $path . $v['img'] ?>" alt="img">
                                <div class="description">
                                    <div>Serial number: <?= $v['s_num'] ?></div>
                                    <div>Price: <?= $v['price'] ?></div>
                                    <div>Production date: <?= $v['year'] ?></div>
                                    <div>Production country: <?= $v['country'] ?></div>
                                    <div class="details">
                                        <a href="index.php?id=<?= $v['id'] ?>" class="buy-item more">More</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            } else {
            ?>
            <a href="index.php?section=<?= $one_product['id_section'] ?>" class="buy-item more">Back</a>
            <div class="product">
                <div class="item-name"><?= $one_product['name'] ?></div>
                <img src="<?= $path . $one_product['img'] ?>" alt="img">
                <div class="description">
                    <div>Serial number: <?= $one_product['s_num'] ?></div>
                    <div>Price: <?= $one_product['price'] ?></div>
                    <div>Production date: <?= $one_product['year'] ?></div>
                    <div>Production country: <?= $one_product['country'] ?></div>
                    <?php foreach ($one_product['param'] as $key => $value) { ?>
                        <div><?= $value['name'] . " : " . $value['value'] ?></div>
                    <?php } ?>
                    <div class="details">
                        <input type="button" class="buy-item more" value="Add to card">
                    </div>
                </div>
            </div>
            <form action="" name="review" method="POST" class="form2">
                <input type="hidden" name="hidden_input" value="first">
                <div class="block">
                    Input name:
                    <input name="name" id="name" type="text" required>
                </div>
                <div class="block">
                    Input email:
                    <input name="email" id="email" type="email" required>
                </div>
                <div class="block">
                    Write comment:(max 200 symbols)
                </div>
                <div class="block">
                    <textarea maxlength="200" required class="area" name="comment"></textarea>
                </div>
                <div class="block">
                    <input type="submit" class="buy-item more" value="Submit">
                </div>
            </form>
        </div>
        <?php
        //print comments
        foreach ($comments as $key => $value) { ?>
            <div class="comment">
                <div>User : <?= $value['name'] ?></div>
                <div>Email: <a href="mailto:<?= $value['email'] ?>"><?= $value['email'] ?></a></div>
                <blockquote class="quote"><?= $value['comment'] ?></blockquote>
            </div>
        <?php }
        } ?>
    </div>
</div>




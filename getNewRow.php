<?php if (isset($_POST['addRow'])) { ?>
    <link href="css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
    <script src="js/bootstrap-tagsinput.min.js" type="text/javascript"></script>
    <div class="row col-lg-12">
        <div class="col-lg-6">
            <input type="text"  class="form-control main" placeholder="Main Territory" width="50%">
        </div>
        <div class="col-lg-5">
            <input type="text"  class="form-control sub" placeholder="Sub Territory" width="50%"  data-role="tagsinput" >                                    
        </div>
    </div>
    <?php
} elseif (isset($_POST['addBusinessRow'])) {
    echo 'reached here';
    require_once(dirname(__FILE__) . "/includes/initialize.php");

    function ProductList($id = "") {
        $Products = Product::find_all();
        $ProductList = '<option value ="" data-rate="0" > Select Brand</option> ';
        foreach ($Products as $Product) {
            if ($id == $Product->id) {
                $ProductList .='<option value ="' . $Product->id . '" data-rate="' . $Product->pts . '" selected>' . $Product->name . '</option>';
            } else {
                $ProductList .='<option value ="' . $Product->id . '" data-rate="' . $Product->pts . '" >' . $Product->name . '</option>';
            }
        }
        return $ProductList;
    }
    ?>
    <tr>
        <td>
            <select class="form-control brandlist commonrate" name="brandlist[]" >
                <?php
                echo ProductList();
                ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="total[]" class="ptsrate" value="0">
            <input type="text" class="form-control common brand" name="brand1[]" maxlength="50" value="0" />
        </td>
    </tr>
<?php }
?>

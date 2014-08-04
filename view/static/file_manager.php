<?php
/*
App Title: Manager Fisiere
App Description:
App Size: 1
App Style:
App Icon: folder
*/
?>
<?/*
 * Old version / l-am inlocuit cu file-managerul gata facut din tema, l-am gasit dupa ce l-am pus pe asta (si e si mai bun)
 * 
<script type="text/javascript">
    
    $(document).ready( function() {
        
        $('#fileManager').gsFileManager({ script: '<?php echo get_template_directory_uri(); ?>/script/fileManager/connectors/GsFileManager.php' });
        
    });
</script>
<div id="fileManager">

</div>
*/ ?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2><i class="fa fa-picture-o"></i> Manager Fisiere</h2>
            </div>
            <div class="box-content">
                <div class="file-manager"></div>
            </div>
        </div>
    </div><!--/col-->

</div><!--/row-->

    <!-- page scripts -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.elfinder.min.js"></script>
    <script>
        /* ---------- File Manager ---------- */
        var elf = $('.file-manager').elfinder({
            url : '<?php echo get_template_directory_uri(); ?>/assets/misc/elfinder-connector/connector.php'  // connector URL (REQUIRED)
        }).elfinder('instance');
    </script>
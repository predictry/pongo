<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>{{$tableHeader}} </h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if (!is_null($contents) && is_array($contents))
                    foreach ($contents as $content) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><span class="line"><?php echo $content['item']['name']; ?></span></td>
                            <td><?php echo $content['occurences']; ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                else {
                    ?>
                    <tr>
                        <td colspan="3" class="text-center">Data not available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
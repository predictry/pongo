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
                foreach ($contents as $item) {
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><span class="line"><?php echo $item['name']; ?></span></td>
                        <td><?php echo $item['total']; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
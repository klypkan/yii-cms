<ul class="file-items">
    <?php foreach ($fileBrowserList as $item): ?>
        <li class="file-item">
            <?php if ($item["type"] == "image"): ?>
                <img src="<?= $item["url"] ?>" alt="<?= $item["name"] ?>"/>
                <br/>
                <?= $item["name"] ?> <br/>
                <input type="hidden" class="url <?= $item["type"] ?>" value="<?= $item["url"] ?>"/>
            <?php elseif ($item["type"] == "file"): ?>
                <i class="fa fa-file-o fa-2x"></i>
                <br/>
                <?= $item["name"] ?> <br/>
                <input type="hidden" class="url <?= $item["type"] ?>" value="<?= $item["url"] ?>"/>
            <?php
            elseif ($item["type"] == "directory"): ?>
                <i class="fa fa-folder-o fa-3x"></i>
                <br/>
                <?= $item["name"] ?> <br/>
                <input type="hidden" class="url <?= $item["type"] ?>" value="<?= $item["url"] ?>"/>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
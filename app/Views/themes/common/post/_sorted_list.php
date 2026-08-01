<?php
$itemCount = 1;

if (!empty($postListItems)):
    foreach ($postListItems as $listItem):
        if (!$hasAccess && $itemCount > 1) {
            break;
        } ?>
        <div class="ordered-list-item">
            <h3 class="title-post-item">
                <?php if ($post->show_item_numbers) {
                    echo $itemCount . '. ' . esc($listItem->title);
                } else {
                    echo esc($listItem->title);
                } ?>
            </h3>

            <?php if (!empty($listItem->image_default)):
                $imgUrl = getStorageFileUrl($listItem->image_default, $listItem->storage); ?>
                <div class="post-image">
                    <div class="post-image-inner">
                        <img src="<?= esc($imgUrl); ?>" alt="<?= esc($listItem->title); ?>" class="img-fluid" width="856" height="570"/>
                        <figcaption class="img-description"><?= esc($listItem->image_description); ?></figcaption>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($hasAccess): ?>
                <div class="entry-content">
                    <?= $listItem->content; ?>
                </div>
            <?php else:
                echo loadCommonView('premium/_paywall', [
                        'restrictionType' => $restrictionType,
                        'paywallType'     => 'preview'
                ]);
            endif; ?>
        </div>
        <?php
        if (!$hasAccess) {
            break;
        }
        $itemCount++;
    endforeach;
endif;
?>
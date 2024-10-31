<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 25/09/2017
 * Time: 7:38 AM
 */

?>
<div class="nb-item">
    <div class="nb-item-inner">
        <div class="nb-img">
            <img src="<?php echo $site_url; ?>/xml/packages-images/<?php echo $package->PACKAGES_IMAGE ?>" alt="<?php echo $package->PACKAGES_NAME ?>" />
        </div>
        <div class="nb-content">
            <h4><?php echo $package->PACKAGES_NAME ?><?= $tag ?></h4>
            <?php
            if($truncate == 1) {
                $desc = strip_tags($package->PACKAGES_NOTES);
            }
            else {
                $desc = $package->PACKAGES_NOTES;
            }
            $length = strlen($desc);
            ?>
            <div class="nb-description">
                <?php if($truncate == 1): ?>
                    <span class="nb-short"><?php echo substr($desc, 0, 80) ?></span>
                    <span class="nb-ellipsis">...</span>
                    <span class="nb-long" style="display: none;"><?php echo substr($desc, 80) ?></span>
                <?php else: ?>
                    <?php echo $desc; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if($truncate == 1): ?>
            <div class="nb-expand">
                <a href="" class="nb-show"><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                <a href="" class="nb-hide" style="display: none;"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>
            </div>
        <?php endif; ?>
		<div class="nb-actions">
			<div class="nb-price">
				<h4>$<?php echo $package->COST ?></h4>
            </div>
            <?php
            $nb_button_style == true ? $expanded = 'expanded' : $expanded = '';
            ?>
			<div class="nb-button-group <?= $expanded ?>">
                <?php 
                $packages_group = $package->GROUPPACKAGES_GROUP;
                if((int) $packages_group <= -1) $packages_group = 1;

                if ($package->PACKAGES_PUBLIC_GIFT == 'Y'): ?>
				
					<a href="<?php echo
                        get_option('nb_base_url') .
                        get_option('nb_business_id') .
                        '/gift/' .
                        $package->PACKAGESDISPCAT_DISPCAT .
                        '?package=' . $packages_group . '-' .
                        $package->PACKAGES_PACKAGE . '#' .
                        $package->PACKAGES_NAME;
					?>" class="nb-button" target="_blank"><i class="fas fa-gift"></i>&nbsp Gift</a>
					
				<?php endif; ?>
				<?php if ($package->PACKAGES_PUBLIC_BOOK == 'Y'): ?>
				
                    <a href="<?php echo 
                        get_option('nb_base_url') . 
					    get_option('nb_business_id') . '/bookpackage?package=' . $package->PACKAGES_PACKAGE; 
					?>" class="nb-button" target="_blank"><i class="fas fa-bookmark"></i>&nbsp Book</a>
					
				<?php endif; ?>
			</div>
		</div>
    </div>
</div>
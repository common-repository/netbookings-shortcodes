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
            <img src="<?php echo $site_url; ?>/xml/services-images/<?php echo $service->SERVICES_IMAGE ?>" alt="<?php echo $service->SERVICES_NAME ?>" />
        </div>
        <div class="nb-content">
            <h4><?php echo $service->SERVICES_NAME ?></h4>
            <?php
            if($truncate == 1) {
                $desc = strip_tags($service->SERVICES_NOTES);
            }
            else {
                $desc = $service->SERVICES_NOTES;
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
				<h4>$<?php echo $service->SERVICES_BASE_COST ?></h4>
            </div>
            <?php
            $nb_button_style  == true ? $expanded = 'expanded' : $expanded = '';
            ?>
			<div class="nb-button-group <?= $expanded ?>">
				<?php if ($service->SERVICES_PUBLIC_GIFT == 'Y'): ?>
				
                    <a href="<?php echo 
                            get_option('nb_base_url') .
                            get_option('nb_business_id') .
                            '/gift/' .
                            $service->SERVICEDISPCAT_DISPCAT .
                            '?service=' . $service->SERVICES_SERVICE . '#' .
                            $service->SERVICES_NAME;
					?>" class="nb-button" target="_blank"><i class="fas fa-gift"></i>&nbsp Gift</a>
					
				<?php endif; ?>
				<?php if ($service->SERVICES_PUBLIC_BOOK == 'Y'): ?>
                    <a href="<?php echo 
                            get_option('nb_base_url') . 
                            get_option('nb_business_id') . 
                            '/bookpackage?content=' . $service->SERVICES_TYPE . $service->SERVICES_SERVICE; 
					?>" class="nb-button" target="_blank"><i class="fas fa-bookmark"></i>&nbsp Book</a>
					
				<?php endif; ?>
			</div>
		</div>
    </div>
</div>





<?php
/**
 * Copyright (c) 2019 Younify D.O.O.
 * All rights reserved.
 *
 * LICENSE
 *
 * All material in this file is, unless otherwise stated, the property of
 * Younify D.O.O. Copyright and other intellectual property laws protect
 * these materials. Reproduction or retransmission of the materials, in whole
 * or in part, in any manner, without the prior written consent of the copyright
 * holder, is a violation of copyright law.
 *
 * @copyright  Copyright (c) 2019 Younify D.O.O.
 * @author     alemar@younify.nl
 */

namespace Younify\ProductCollections\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Times extends AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
    }

    public function getConfigTimezone() {
        return $this->timezone->getConfigTimezone();
    }

    // TODO: Remove this function.
    /**
     * 2019-10-04 09:40:55
     */
    public function getSpecialDate($websiteId = null)
    {
        return $this->getScopeValue('special_date', $websiteId);
    }

}

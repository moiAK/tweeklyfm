<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace App\Presenters;

/**
 * Class UpdatePresenter.
 */
class UpdatePresenter
{
    /**
     * @var
     */
    private $update;

    /**
     * @param Update $update
     */
    public function _construct(Update $update)
    {
    }

    /**
     * @return mixed
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param mixed $update
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    }
}

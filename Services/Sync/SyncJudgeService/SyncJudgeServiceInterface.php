<?php

namespace MauticPlugin\MauticIntegrationsBundle\Services\Sync\SyncJudgeService;

use MauticPlugin\MauticIntegrationsBundle\DAO\Sync\InformationChangeRequestDAO;

/**
 * Interface SyncJudgeServiceInterface.
 */
interface SyncJudgeServiceInterface
{
    /**
     * Winner is selected only if provided vindications don't leave open possibilities of different result.
     */
    const PRESUMPTION_OF_INNOCENCE_MODE = 'presumptionOfInnocence';

    /**
     * Winner is selected based on certain information only.
     */
    const HARD_EVIDENCE_MODE            = 'hardEvidence';

    /**
     * Winner is selected based on best evidence available.
     */
    const BEST_EVIDENCE_MODE            = 'bestEvidence';

    /**
     * @param string $mode
     * @param InformationChangeRequestDAO $changeRequest1
     * @param InformationChangeRequestDAO $changeRequest2
     * @return mixed New value
     */
    public function adjudicate($mode = self::PRESUMPTION_OF_INNOCENCE_MODE, InformationChangeRequestDAO $changeRequest1, InformationChangeRequestDAO $changeRequest2);
}
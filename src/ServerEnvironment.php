<?php
declare(strict_types=1);
namespace ReactiveSlim;

final class ServerEnvironment
{
    /** @var int DEVELOPMENT  */
    const DEVELOPMENT = 3;
    /** @var int TESTING */
    const TESTING     = 2;
    /** @var int STAGING */
    const STAGING     = 1;
    /** @var int PRODUCTION */
    const PRODUCTION  = 0;

    /**
     * @param int $environment
     * @return string
     */
    public static function getEnvironmentName(int $environment) :string
    {
        switch ($environment) {
            case ServerEnvironment::PRODUCTION:
                $environmentName = 'Production';
                break;
            case ServerEnvironment::STAGING:
                $environmentName = 'Staging';
                break;
            case ServerEnvironment::TESTING:
                $environmentName = 'Testing';
                break;
            case ServerEnvironment::DEVELOPMENT:
                $environmentName = 'Development';
                break;
            default:
                $environmentName = 'Production';
                break;
        };

        return $environmentName;
    }
}

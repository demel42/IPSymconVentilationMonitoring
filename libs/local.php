<?php

declare(strict_types=1);

trait VentilationMonitoringLocalLib
{
    private function GetFormStatus()
    {
        $formStatus = $this->GetCommonFormStatus();

        return $formStatus;
    }

    public static $STATUS_INVALID = 0;
    public static $STATUS_VALID = 1;
    public static $STATUS_RETRYABLE = 2;

    private function CheckStatus()
    {
        switch ($this->GetStatus()) {
            case IS_ACTIVE:
                $class = self::$STATUS_VALID;
                break;
            default:
                $class = self::$STATUS_INVALID;
                break;
        }

        return $class;
    }

    public static $CLOSURE_STATE_CLOSE = 0;
    public static $CLOSURE_STATE_TILT = 1;
    public static $CLOSURE_STATE_OPEN = 2;

    public static $RISK_OF_MOLD_NONE = 0;
    public static $RISK_OF_MOLD_WARN = 1;
    public static $RISK_OF_MOLD_ALARM = 2;

    private function InstallVarProfiles(bool $reInstall = false)
    {
        if ($reInstall) {
            $this->SendDebug(__FUNCTION__, 'reInstall=' . $this->bool2str($reInstall), 0);
        }

        $associations = [
            ['Wert' => false, 'Name' => $this->Translate('No'), 'Farbe' => -1],
            ['Wert' => true, 'Name' => $this->Translate('Yes'), 'Farbe' => -1],
        ];
        $this->CreateVarProfile('VentilationMonitoring.ReduceHumidityPossible', VARIABLETYPE_BOOLEAN, '', 0, 0, 0, 0, '', $associations, $reInstall);
        $this->CreateVarProfile('VentilationMonitoring.YesNo', VARIABLETYPE_BOOLEAN, '', 0, 0, 0, 0, '', $associations, $reInstall);

        $associations = [
            ['Wert' => self::$CLOSURE_STATE_CLOSE, 'Name' => $this->Translate('Close'), 'Farbe' => -1],
            ['Wert' => self::$CLOSURE_STATE_TILT, 'Name' => $this->Translate('Tilt'), 'Farbe' => -1],
            ['Wert' => self::$CLOSURE_STATE_OPEN, 'Name' => $this->Translate('Open'), 'Farbe' => -1],
        ];
        $this->CreateVarProfile('VentilationMonitoring.ClosureState', VARIABLETYPE_INTEGER, '', 0, 0, 0, 0, '', $associations, $reInstall);

        $associations = [
            ['Wert' => self::$RISK_OF_MOLD_NONE, 'Name' => $this->Translate('None'), 'Farbe' => -1],
            ['Wert' => self::$RISK_OF_MOLD_WARN, 'Name' => $this->Translate('Warning'), 'Farbe' => -1],
            ['Wert' => self::$RISK_OF_MOLD_ALARM, 'Name' => $this->Translate('Alarm'), 'Farbe' => -1],
        ];
        $this->CreateVarProfile('VentilationMonitoring.RiskOfMold', VARIABLETYPE_INTEGER, '', 0, 0, 0, 0, '', $associations, $reInstall);

        $this->CreateVarProfile('VentilationMonitoring.AbsoluteHumidity', VARIABLETYPE_FLOAT, ' g/m³', 0, 0, 0, 0, 'Drops', [], $reInstall);
        $this->CreateVarProfile('VentilationMonitoring.SpecificHumidity', VARIABLETYPE_FLOAT, ' g/kg', 0, 0, 0, 0, 'Drops', [], $reInstall);
        $this->CreateVarProfile('VentilationMonitoring.Dewpoint', VARIABLETYPE_FLOAT, ' °C', 0, 30, 0, 0, 'Drops', [], $reInstall);
    }

    public static $TIMEUNIT_SECONDS = 0;
    public static $TIMEUNIT_MINUTES = 1;
    public static $TIMEUNIT_HOURS = 2;
    public static $TIMEUNIT_DAYS = 3;

    private function GetTimeunitAsOptions()
    {
        return [
            [
                'value'   => self::$TIMEUNIT_SECONDS,
                'caption' => $this->Translate('Seconds'),
            ],
            [
                'value'   => self::$TIMEUNIT_MINUTES,
                'caption' => $this->Translate('Minutes'),
            ],
            [
                'value'   => self::$TIMEUNIT_HOURS,
                'caption' => $this->Translate('Hours'),
            ],
            [
                'value'   => self::$TIMEUNIT_DAYS,
                'caption' => $this->Translate('Days'),
            ],
        ];
    }

    private function CalcByTimeunit(int $unit, int $val)
    {
        switch ($unit) {
            case self::$TIMEUNIT_SECONDS:
                $mul = 1;
                break;
            case self::$TIMEUNIT_MINUTES:
                $mul = 60;
                break;
            case self::$TIMEUNIT_HOURS:
                $mul = 60 * 60;
                break;
            case self::$TIMEUNIT_DAYS:
                $mul = 60 * 60 * 24;
                break;
            default:
                $mul = 0;
                break;
        }
        return $val * $mul;
    }

    private function Timeunit2Suffix(int $unit)
    {
        switch ($unit) {
            case self::$TIMEUNIT_SECONDS:
                $s = 's';
                break;
            case self::$TIMEUNIT_MINUTES:
                $s = 'm';
                break;
            case self::$TIMEUNIT_HOURS:
                $s = 'h';
                break;
            case self::$TIMEUNIT_DAYS:
                $s = 'd';
                break;
            default:
                $s = '';
                break;
        }
        return $s;
    }
}

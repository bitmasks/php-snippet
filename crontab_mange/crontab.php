<?php

/**
 * Class Crontab
 * PHP Linux Crontab Web
 * Crontab 网页版管理工具
 *注意执行crontab的用户要是root用户
 */
class Crontab
{

    /**
     * Location of the crontab executable
     *
     * @var string
     */
    var $crontab = '/usr/bin/crontab';

    /**
     * Location to save the crontab file.
     *
     * @var string
     */
    var $destination = '/tmp/CronManager';

    /**
     * Minute (0 - 59)
     *
     * @var string
     */
    var $minute = '*';

    /**
     * Hour (0 - 23)
     *
     * @var string
     */
    var $hour = '*';

    /**
     * Day of Month (1 - 31)
     *
     * @var string
     */
    var $dayOfMonth = '*';

    /**
     * Month (1 - 12) OR jan,feb,mar,apr...
     *
     * @var string
     */
    var $month = '*';

    /**
     * Day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
     *
     * @var string
     */
    var $dayOfWeek = '*';

    /**
     * @var array
     */
    var $jobs = array();

    /*function Crontab()
    {
    }*/
    function __construct()
    {
    }

    /**
     * Set minute or minutes
     *
     * @param string $minute required
     *
     * @return object
     */
    function onMinute($minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Set hour or hours
     *
     * @param string $hour required
     *
     * @return object
     */
    function onHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Set day of month or days of month
     *
     * @param string $dayOfMonth required
     *
     * @return object
     */
    function onDayOfMonth($dayOfMonth)
    {
        $this->dayOfMonth = $dayOfMonth;

        return $this;
    }

    /**
     * Set month or months
     *
     * @param string $month required
     *
     * @return object
     */
    function onMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Set day of week or days of week
     *
     * @param string $minute required
     *
     * @return object
     */
    function onDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * Set entire time code with one function. This has to be a complete entry. See http://en.wikipedia.org/wiki/Cron#crontab_syntax
     *
     * @param string $timeCode required
     *
     * @return object
     */
    function on($timeCode)
    {
        list(
            $this->minute,
            $this->hour,
            $this->dayOfMonth,
            $this->month,
            $this->dayOfWeek
            ) = explode(' ', $timeCode);

        return $this;
    }

    /**
     * Add job to the jobs array. Each time segment should be set before calling this method. The job should include the absolute path to the commands being used.
     *
     * @param string $job required
     *
     * @return object
     */
    function doJob($job)
    {
        $this->jobs[] = $this->minute . ' ' .
            $this->hour . ' ' .
            $this->dayOfMonth . ' ' .
            $this->month . ' ' .
            $this->dayOfWeek . ' ' .
            $job;

        return $this;
    }

    /**
     * Save the jobs to disk, remove existing cron
     *
     * @param boolean $includeOldJobs optional
     *
     * @return boolean
     */
    function activate($includeOldJobs = TRUE)
    {
        $contents = implode("\n", $this->jobs);
        $contents .= "\n";

        try {
            if ($includeOldJobs) {
                $oldJobs = $this->listJobs();
                foreach ($oldJobs as $job) {
                    $contents .= $job;
                    $contents .= "\n";
                }
            }

            if (is_writable($this->destination) || !file_exists($this->destination)) {
                exec($this->crontab . ' -r;');
                file_put_contents($this->destination, $contents, LOCK_EX);
                exec($this->crontab . ' ' . $this->destination . ';');
                exec('service crond restart'); //重新加载配置文件

                return TRUE;
            }
        } catch (Exception $e) {
            throw new Exception("ERROR: " . $e->getMessage(), 1);
        }
        return FALSE;
    }

    /**
     * Deletes all crontab entries.
     */
    function deleteAllJobs()
    {
        try {
            if (is_writable($this->destination) || !file_exists($this->destination)) {
                exec($this->crontab . ' -r;');
                file_put_contents($this->destination, '', LOCK_EX);
                exec($this->crontab . ' ' . $this->destination . ';');

                return TRUE;
            }
        } catch (Exception $e) {
            throw new Exception("ERROR: " . $e->getMessage(), 1);
        }


    }

    /**
     * Deletes a specific crontab entry.
     */
    function deleteJob($id)
    {
        try {
            $allJobs = $this->listJobs();
            foreach ($allJobs as $key => $job) {
                if ($key == $id) {
                    unset($allJobs[$key]);
                }
            }

            // Update the crontab
            $this->deleteAllJobs();

            $contents = '';
            foreach ($allJobs as $job) {
                $contents .= $job;
                $contents .= "\n";
            }

            if (is_writable($this->destination) || !file_exists($this->destination)) {
                exec($this->crontab . ' -r;');
                file_put_contents($this->destination, $contents, LOCK_EX);
                exec($this->crontab . ' ' . $this->destination . ';');

                return TRUE;
            }
        } catch (Exception $e) {
            throw new Exception("ERROR: " . $e->getMessage(), 1);
        }


    }

    /**
     * List current cron jobs
     *
     * @return string
     */
    function listJobs()
    {
        try {
            $result = exec($this->crontab . ' -l;', $output, $retval);

            return $output;
        } catch (Exception $e) {
            throw new Exception("ERROR: " . $e->getMessage(), 1);
        }
    }
}

$param = $_REQUEST;
$cron = new Crontab();
if ($param['op'] == 'active') {
    $active = $cron->listJobs();
    echo json_encode($active);
} else if ($param['op'] == 'add') {
    if (isset($_POST['minute']) && $_POST['minute'] != '') $cron->onMinute($_POST['minute']);
    if (isset($_POST['hour']) && $_POST['hour'] != '') $cron->onHour($_POST['hour']);
    if (isset($_POST['month']) && $_POST['month'] != '') $cron->onMonth($_POST['month']);
    if (isset($_POST['dayweek']) && $_POST['dayweek'] != '') $cron->onDayOfWeek($_POST['dayweek']);
    if (isset($_POST['daymonth']) && $_POST['daymonth'] != '') $cron->onDayOfMonth($_POST['daymonth']);
    if (isset($_POST['command']) && $_POST['command'] != '') $cron->doJob($_POST['command']);

    if ($cron->activate()) {
        echo json_encode($cron);
    } else {
        echo FALSE;
    }
} else if ($param['op'] == 'deleteall') {
    $cron->deleteAllJobs();

} else if ($param['op'] == 'deletejob') {
    $cron->deleteJob($_POST['jobid']);
} else {
    //测试
    @$time = "\n\t" . date('Y-m-d H:i:s', time()) . "--------------\n\t";
    echo file_put_contents('1.log', $time, FILE_APPEND);

}
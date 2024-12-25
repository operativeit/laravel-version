<?php

namespace EomPlus\Version\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Version extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version {cmd?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected static function execCommand($cmd)
    {
        $process = Process::fromShellCommandline($cmd);
        $output = '';

        $process->setTimeout(null)
            ->run(function ($type, $line) use (&$output) {
                $output.= $line;
            });

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $output;
    }

    public static function getHash()
    {
        return trim(self::execCommand('git log --pretty="%h" -n1 HEAD'));
    }

    public static function getDate()
    {
        return Carbon::parse(trim(self::execCommand('git log -n1 --pretty=%ci HEAD')));
    }

    public static function createTag(&$version)
    {
        $tag = Str::replaceMatches('/{([^}]+)}/', function (array $matches) use ($version) {
            return $version->{$matches[1]};
        }, 'v{major}.{minor}.{patch}');

        echo self::execCommand('git add '. base_path('version.json'));
        echo self::execCommand('git commit -m "prepare to release'.$tag.'"');
        echo  self::execCommand('git push');

        $cmd = Str::replaceArray('?', [$tag, 'version '.$tag.' is released'], "git tag -a ? -m '?'");
        self::execCommand($cmd);

        $cmd = Str::replaceArray('?', [$tag], 'git push origin ?');
        self::execCommand($cmd);

        $version->hash = self::getHash();
        $version->date = self::getDate()->format('d/m/y H:i');

	echo 'version '.$tag.' is released' . PHP_EOL;
    }

    public static function getVersion()
    {
        if (! file_exists(base_path('version.json'))) {
            return [
                'major' => 0,
                'minor' => 0,
                'patch' => 0,
            ];
        } else {
            return json_decode(file_get_contents(base_path('version.json')), false, 512, JSON_THROW_ON_ERROR);
        }
    }

    public static function saveVersion($version)
    {
        file_put_contents(base_path('version.json'), json_encode($version, JSON_PRETTY_PRINT));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $version = self::getVersion();

            if ($cmd = $this->argument('cmd')) {
                switch ($cmd) {
                    case 'patch':
                        $version->patch += 1;
                        break;
                    case 'major':
                        $version->major += 1;
                        break;
                    case 'minor':
                        $version->minor += 1;
                        break;
                    default:
                        throw new \Exception('unknown command');
                }
                self::saveVersion($version);
                self::createTag($version);
            }

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

<?php

namespace EomPlus\Version\Console\Commands;

use Exception;
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
    protected $signature = 'version
                            {cmd? : [ <newversion> |  major | minor | patch | commit ] }';

    protected $version;

    protected $filePath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command allows to increment  major, minor and patch version as well create and commit tag for the release';

    protected static function execCommand($cmd)
    {
        $process = Process::fromShellCommandline($cmd);
        $output = '';

        $process->setTimeout(null)
            ->run(function ($type, $line) use (&$output) {
                $output .= $line;
            });

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $output;
    }

    public function formatVersion($format = 'v{major}.{minor}.{patch}')
    {
        return preg_replace_callback('/{([^}]+)}/', function ($matches) {
            return $this->version->{$matches[1]};
        }, $format, -1);
    }

    public static function getHash()
    {
        return trim(self::execCommand('git log --pretty="%h" -n1 HEAD'));
    }

    public static function getDate()
    {
        return Carbon::parse(trim(self::execCommand('git log -n1 --pretty=%ci HEAD')));
    }

    public function createTag()
    {
        $tag = $this->formatVersion();
        $currentPath = getcwd();
        if (! file_exists($currentPath.'/.git')) {
            throw new Exception('The current directory is not a git repository');
        }

        echo self::execCommand('git add version.json');
        echo self::execCommand('git commit -m "prepare to release'.$tag.'"');
        echo self::execCommand('git push');

        $cmd = Str::replaceArray('?', [$tag, 'version '.$tag.' is released'], "git tag -a ? -m '?'");
        self::execCommand($cmd);

        $cmd = Str::replaceArray('?', [$tag], 'git push origin ?');
        self::execCommand($cmd);

        $this->version->hash = self::getHash();
        $this->version->date = self::getDate()->format('d/m/y H:i');

        $this->info('Version '.$tag.' is released');
    }

    public function load()
    {
        $current_path = getcwd();
        if (file_exists($current_path.'/version.json')) {
            $this->filePath = $current_path.'/version.json';
            $this->version = json_decode(file_get_contents($current_path.'/version.json'), false, 512, JSON_THROW_ON_ERROR);
        } else {

            if ($this->confirm('No version.json in current directory, do you want to use the one at '.base_path(), false)) {
                if (file_exists(base_path('version.json'))) {
                    $this->version = json_decode(file_get_contents(base_path('version.json')), false, 512, JSON_THROW_ON_ERROR);
                } else {
                    throw new Exception('No version.json at '.base_path());
                }
            } else {
                throw new Exception('No version.json in current directory');
            }
        }

        return true;
    }

    public function save()
    {
        file_put_contents($this->filePath, json_encode($this->version, JSON_PRETTY_PRINT));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->load();

            if ($cmd = $this->argument('cmd')) {
                switch ($cmd) {
                    case 'patch':
                        $this->version->patch += 1;
                        break;
                    case 'major':
                        $this->version->major += 1;
                        break;
                    case 'minor':
                        $this->version->minor += 1;
                        break;
                    case 'commit':
                        $this->createTag();
                        break;
                    default:
			if (preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $cmd , $version)) {
                            $this->version = (object) [
                                'major' => $version[1],
                                'minor' => $version[2],
                                'patch' => $version[3],
                            ];
                        } else {
                            throw new \Exception('unknown command');
                        }
                }
                $this->save();
            } else {
                $this->info($this->formatVersion());
            }

        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return 1;
        }
    }
}

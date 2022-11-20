@setup
$branch = isset($branch) ? $branch : "master";
$serverUser = 'deployer';
$rootDirectory = '~/home/' . $serverUser;

$server = $serverUser . '@45.159.113.240';
@endsetup

@servers(['production' => $server])

@task('clone', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}'
cd {{ $rootDirectory }}

echo '>> mkdir {{ $rootDirectory }}/project'
mkdir {{ $rootDirectory }}/project

echo '>> chmod 755 {{ $rootDirectory }}/project'
chmod 755 {{ $rootDirectory }}/project

echo '>> cd {{ $rootDirectory }}/project'
cd {{ $rootDirectory }}/project

echo '<< git clone git@github.com:hardworker092/cms.git deploy'
git clone git@github.com:hardworker092/cms.git deploy
@endtask

@task('environment', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}/project/deploy'
cd {{ $rootDirectory }}/project/deploy

echo '<< cp .env.example .env'
cp .env.example .env

echo '>> SSH to your server, paste your valid .env credentials & save them. Then run envoy run post-deploy'
@endtask

@task('composer-install', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}/project/deploy'
cd {{ $rootDirectory }}/project/deploy

echo '<< /home/{{ $serverUser }}/bin/composer.phar install --prefer-dist --no-scripts --no-dev -q -o'

/home/{{ $serverUser }}/bin/composer.phar install --prefer-dist --no-scripts --no-dev -q -o
@endtask

@task('composer-update', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}/project/deploy'
cd {{ $rootDirectory }}/project/deploy

echo '<< /home/{{ $serverUser }}/bin/composer.phar dump -o && php artisan optimize'

/home/{{ $serverUser }}/bin/composer.phar dump -o && php artisan optimize

@endtask


@task('migrate', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}/project/deploy'
cd {{ $rootDirectory }}/project/deploy

php artisan migrate --force;
@endtask

@task('symlink', ['on' => 'production'])
echo '<< ln -s /home/{{ $serverUser }}/project/deploy/public /var/www/html'

ln -s /home/{{ $serverUser }}/project/deploy/public /var/www/html
@endtask

@task('deploy-changes', ['on' => 'production'])
echo '>> cd {{ $rootDirectory }}/project/deploy'
cd {{ $rootDirectory }}/project/deploy

echo '>> git checkout {{ $branch }}'
git checkout {{ $branch }}

echo '<< git pull --rebase'
git pull --rebase
@endtask

@story('deploy', ['on' => 'production'])
setup
environment
@endstory

@story('post-deploy', ['on' => 'production'])
composer-install
composer-update
migrate
symlink
@endstory

@story('update')
deploy-changes
composer-update
migrate
@endstory
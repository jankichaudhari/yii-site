<?php

class debugTools
{
	public $basepath;
	public $newpath;
	public $name = 'phpstorm_devtools';
	public $target = 'phpstormlink';
	public $protocol = 'phpstorm:';

	public function init()
	{

		register_shutdown_function(function () {

			$files       = get_included_files();
			$controllers = [];
			$models      = [];
			$rest        = [];
			$views       = [];

			foreach ($files as $file) {
				if (strpos($file, 'htdocs') === false) {
					continue;
				}
				$file = str_replace($this->basepath, $this->newpath, $file);

				if (strpos($file, 'Controller') !== false) {
					$controllers[] = $file;
					continue;
				}

				if (strpos($file, '/models/') !== false) {
					$models[] = $file;
					continue;
				}
				if (strpos($file, '/views/') !== false) {
					$views[] = $file;
					continue;
				}
				$rest[] = $file;
			}
			?>
        <script id="<?php echo $this->name ?>" type="text/<?php echo $this->name ?>"><h4>
            Controllers
        </h4>
            <ul>
				<?php foreach ($controllers as $key=> $value): ?>
                <li><a target="<?php echo $this->target ?>" href="<?php echo $this->protocol ?><?php echo $value ?>"><?php echo $value ?></a></li>
				<?php endforeach; ?>
            </ul>
            <h4>
                Models
            </h4>
            <ul>
				<?php foreach ($models as $key=> $value): ?>
                <li><a target="<?php echo $this->target ?>" href="<?php echo $this->protocol ?><?php echo $value ?>"><?php echo $value ?></a></li>
				<?php endforeach; ?>
            </ul>
            <h4>
                Views
            </h4>
            <ul>
				<?php foreach ($views as $key=> $value): ?>
                <li><a target="<?php echo $this->target ?>" href="<?php echo $this->protocol ?><?php echo $value ?>"><?php echo $value ?></a></li>
				<?php endforeach; ?>
            </ul>
            <h4>
                Rest
            </h4>
            <ul>
				<?php foreach ($rest as $key=> $value): ?>
                <li><a target="<?php echo $this->target ?>" href="<?php echo $this->protocol ?><?php echo $value ?>"><?php echo $value ?></a></li>
				<?php endforeach; ?>
            </ul>
        </script>

		<?php
		});

	}
}

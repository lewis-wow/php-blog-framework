<?php

use Michelf\MarkdownExtra;

class FileSystemRouter {
	protected const basePath = '/';

	protected const specialPaths = [
		"Error.md" => "Error.md",
		"Layout.php" => "Layout.php"
	];

	protected const filters = [
		"content" => '/\.md$/',
		"style" => '/\.css$/',
		"meta" => '/\.json$/'
	];

	public function __construct($root) {
		$this->router = new \Bramus\Router\Router();
		$this->router->setBasePath(self::basePath);
		$this->root = $root;
		$this->layout = $root . '/Layout.php';
		$this->error = $root . '/Error.md';

		if(is_file($this->error)) {
			$this->router->set404(function() {
				$content = $this->getMarkdown($this->error);
				$css = '';
				require_once $this->layout;
			});
		}
	}

	protected function getMarkdown($file) {
		return MarkdownExtra::defaultTransform(file_get_contents($file));
	}

	protected function getFolderContent($dir) {
		$files = scandir($dir);
		$contents = [
			"css" => []
		];

		foreach($files as $key=>$value) {
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			$relativePath = str_replace($this->root, '', $path);

			if(is_dir($path) && $value != "." && $value != "..") {
				$this->router->mount($relativePath, function() use($path) {
					$this->getFolderContent($path);
				});
				continue;
			}

			if(array_key_exists($relativePath, self::specialPaths)) {
				continue;
			}

			if(preg_match(self::filters['style'], $path)) {
				$contents['css'][] = '<link rel="stylesheet" href="routes' . $relativePath . '">';
				continue;
			}

			if(preg_match(self::filters['content'], $path)) {
				$fileName = preg_replace(self::filters['content'], '', str_replace($dir, '', $path));

				$this->router->get($fileName === '/index' ? '/' : $fileName, function() use($path, $contents) {
					$css = implode("\n", $contents["css"]);
					$content = $this->getMarkdown($path);

					require_once $this->layout;
				});
			}
		}
	}

	public function run() {
		$this->getFolderContent($this->root);
		$this->router->run();
	}
}

<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__ . '/src')
    )
;

<?php

namespace Air\Package\Wechat;

class WechatMedia
{
  const SCAN_QRCODE_TIPS = [
    'test' => [
      'huixintong' => [
        'huixintong' => 'rXEHY4_d3Th4tccy7ZrAJ1gx-08cjErMqjaau8T3zCE',
        'tizhijian' => 'rXEHY4_d3Th4tccy7ZrAJygX1GxOtlyOZP0MobQZIrs',
        'zhongyou'  => 'rXEHY4_d3Th4tccy7ZrAJ7ZXOSUvWAs4cFiOcgdmKl0',
        'yingtong'  => 'rXEHY4_d3Th4tccy7ZrAJ3sSRdrxWsVA6fFL79BCM9o',
        'taiping'  => 'rXEHY4_d3Th4tccy7ZrAJ8OlFUUqOZ3AUj7xi_alleI',
        'yt_health' => '',

      ],
      'tizhijian' => [
        'huixintong' => 'DyXplSn4cdctcHmtvf7sGtVvNT6OQfNPHfI_jjLvgug',
        'zhongyou'  => 'DyXplSn4cdctcHmtvf7sGo5MmOWmwpPV6An3NYGJLmY',
        'yingtong'  => 'DyXplSn4cdctcHmtvf7sGpIiuypTNoY94SakRsg-7dE',
        'taiping'  => 'DyXplSn4cdctcHmtvf7sGqEPWIdqUcfJH39ChWvEXhc',
        'tizhijian' => 'DyXplSn4cdctcHmtvf7sGqEFAqKjY_4AFaOAaVrPc4U',
        'yt_health' => '',
      ],
      'zhongyou'  => [
        'huixintong' => 'h4zL1Z9gzcRkbW-KlkRZkm1_6QnMIEVrDvWtN1WHLhc',
        'tizhijian' => 'h4zL1Z9gzcRkbW-KlkRZklMP_CnENOZwgN2HSn3vHwQ',
        'yingtong'  => 'h4zL1Z9gzcRkbW-KlkRZkm4F9Z7JZIygW2h33uFiDtA',
        'taiping'  => 'h4zL1Z9gzcRkbW-KlkRZktAPl5FUi6d4TOomxkZeXmY',
        'zhongyou' => 'h4zL1Z9gzcRkbW-KlkRZkl0lY23fMHiNQy6QqsoH4Jc',
        'yt_health' => '',
      ],
      'yingtong'  => [
        'yingtong' => 'R7i6BPiBnFlVTVSOGSdEMutcB0LVXxa1MegU9YQSVfg',
        'huixintong' => 'R7i6BPiBnFlVTVSOGSdEMvNk-PnaxDaGfecGFHh0OWY',
        'tizhijian' => 'R7i6BPiBnFlVTVSOGSdEMsmyIRy1hWFgVr00B8XwlV4',
        'zhongyou'  => 'R7i6BPiBnFlVTVSOGSdEMvX6e9LJz_p91NeWIqRhurg',
        'taiping'  => 'R7i6BPiBnFlVTVSOGSdEMtePzZ0LmEwfKf99Pe6cd8o',
        'yt_health' => '',
      ],
      'yt_health'  => [
        'yingtong' => 'R7i6BPiBnFlVTVSOGSdEMutcB0LVXxa1MegU9YQSVfg',
        'huixintong' => 'R7i6BPiBnFlVTVSOGSdEMvNk-PnaxDaGfecGFHh0OWY',
        'tizhijian' => 'R7i6BPiBnFlVTVSOGSdEMsmyIRy1hWFgVr00B8XwlV4',
        'zhongyou'  => 'R7i6BPiBnFlVTVSOGSdEMvX6e9LJz_p91NeWIqRhurg',
        'taiping'  => 'R7i6BPiBnFlVTVSOGSdEMtePzZ0LmEwfKf99Pe6cd8o',
        'yt_health' => '',
      ],
    ],
    'production' => [
      'huixintong' => [
        'huixintong' => 'tjn0AeqenLGIqv52LxB9iK_f5yjaGvBlj7K5gAcEjtE',
        'tizhijian' => 'tjn0AeqenLGIqv52LxB9iMiHIZ72c8HlirpyN9f_vjE',
        'zhongyou'  => 'tjn0AeqenLGIqv52LxB9iHS0VYYRP7PWsrh_6bmCG3Q',
        'yingtong'  => 'tjn0AeqenLGIqv52LxB9iBS8OLjAb0cayhVUOPwfNGg',
        'taiping'  => 'tjn0AeqenLGIqv52LxB9iPSWgF8EKdIVU9-N1e5oe08',
        'yt_health' => '',
      ],
      'tizhijian' => [
        'huixintong' => '2Xa98Q9ZMC2x2UP8C5itXZVoMeBbvCKUge1IHAGSHv0',
        'zhongyou'  => '2Xa98Q9ZMC2x2UP8C5itXQgEd6fJokNyfnqtAAVCY6A',
        'yingtong'  => '2Xa98Q9ZMC2x2UP8C5itXdFMvugG7mpWv3OsrH2IJ5Q',
        'taiping'  => '2Xa98Q9ZMC2x2UP8C5itXYJg3TmM4xWpRrUIK3yjqF0',
        'tizhijian' => '2Xa98Q9ZMC2x2UP8C5itXcOrtOkJoZHmQ79eBizKNdw',
        'yt_health' => '',
      ],
      'zhongyou'  => [
        'huixintong' => 'rciOi0ysKllhKWM3E4P8R9gIwEJ8kMU4knMbLPZdgyg',
        'tizhijian' => 'rciOi0ysKllhKWM3E4P8R1v6RXJVGAAe0rIsqx-LWJE',
        'yingtong'  => 'rciOi0ysKllhKWM3E4P8R4hx9xbg374Cc_9rbx1o_wQ',
        'taiping'  => 'rciOi0ysKllhKWM3E4P8R68SE96IidZ0KQYrZBqgnhI',
        'zhongyou' => 'rciOi0ysKllhKWM3E4P8R7eZdxdLEQnC_elAJWzRLf8',
        'yt_health' => '',
      ],
      'yingtong'  => [
        'yingtong' => '_qjBmJCj8U6kFq91rK7zsF-rc09PhOO79HWO3ip_b7Q',
        'huixintong' => '_qjBmJCj8U6kFq91rK7zsHTH2sHKqWCtcirlrPLqGDA',
        'tizhijian' => '_qjBmJCj8U6kFq91rK7zsH05ESI4I16N4zfQUg7cDBs',
        'zhongyou'  => '_qjBmJCj8U6kFq91rK7zsCH4BYPq0JL9jhWE8xtoTig',
        'taiping'  => '_qjBmJCj8U6kFq91rK7zsBpGZlPk6upqqGBkKpSGBts',
        'yt_health' => '',
      ],
      'yt_health'  => [
        'yingtong' => 'R7i6BPiBnFlVTVSOGSdEMutcB0LVXxa1MegU9YQSVfg',
        'huixintong' => 'R7i6BPiBnFlVTVSOGSdEMvNk-PnaxDaGfecGFHh0OWY',
        'tizhijian' => 'R7i6BPiBnFlVTVSOGSdEMsmyIRy1hWFgVr00B8XwlV4',
        'zhongyou'  => 'R7i6BPiBnFlVTVSOGSdEMvX6e9LJz_p91NeWIqRhurg',
        'taiping'  => 'R7i6BPiBnFlVTVSOGSdEMtePzZ0LmEwfKf99Pe6cd8o',
        'yt_health' => '',
      ],
    ]
  ];
}

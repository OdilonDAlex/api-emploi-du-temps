<?php

namespace App\Exports;

use App\Models\Logger;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\Process\PhpExecutableFinder;

class AcademicTrackTimetableExport implements
    FromArray,
    WithColumnWidths,
    WithTitle,
    WithDrawings,
    WithCustomStartCell,
    WithEvents
{
    /**
     * @return Array
     */

    private int $rowCount = 0;
    private array $shouldBeMerged = array();

    public function __construct(
        private string $weekOf,
        private string $name,
        private array $timetable,
        private string $today = "",
    ) {}

    public function title(): string
    {
        return $this->name;
    }

    public function drawings()
    {
        $logo = new Drawing();
        $logo->setName('header');
        $logo->setDescription('haut de page');
        $logo->setPath(Storage::disk('public')->path('header.png'));
        $logo->setWidth((18.10  * 96) / 2.54);
        $logo->setHeight((3.85 * 96) / 2.54);
        $logo->setCoordinates('A1');
        return $logo;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function array(): array
    {
        $result = [];
        $exploded = explode(" ", $this->weekOf);
        $year = (int)array_pop($exploded);

        $exploded = explode(" ", $this->name);
        $result[] = ["EMPLOI DU TEMPS"];
        $result[] = ["( " . $this->weekOf . " )"];
        $result[] = ["Mention : Mathématiques, Informatique et Applications", "", "", "Année U : " . $year . "-" . ($year + 1)];
        $result[] = ["Niveau : " . array_shift($exploded), "", "", "Parcous : " . implode(" ", $exploded)];

        $result[] = ["DATE", "HORAIRE", "ELEMENT CONSTITUTIF", "SALLE"];

        // $currentDay = (int)explode(" ", $this->weekOf)[2];

        foreach ($this->timetable as $dayName => $courses) {
            $currentRow = count($result) + 6;
            if (count($courses) > 1) {
                $this->shouldBeMerged[] = ["A" . $currentRow, "A" . ($currentRow + 1)];

                $result[] = [Str::limit($dayName, 3, '.'), "07h-12h", $courses[0]["name"] . " ( " . $courses[0]["professor"] . " )", $courses[0]["classroom"]];
                $result[] = [Str::limit($dayName, 3, '.'), "14h-18h", $courses[1]["name"] . " ( " . $courses[1]["professor"] . " )", $courses[1]["classroom"]];
            } else {
                $result[] = [Str::limit($dayName, 3, '.'), $courses[0]["dayPart"] === "Matin" ? "07h-12h" : "14h-18h", $courses[0]["name"] . " ( " . $courses[0]["professor"] . " )", $courses[0]["classroom"]];
            }

        }

        $result[] = ["*: Tronc commun", "", "Toamasina, le {$this->today}", ""];
        $result[] = ["", "", "Le Chef de mention" . str_repeat(" ", (strlen("Toamasina, le {$this->today}") - strlen("Le Chef de mention")) / 2), ""];
        $result[] = ["", "", "PO Le Secrétaire" . str_repeat(" ", (strlen("Toamasina, le {$this->today}") - strlen("PO Le Secrétaire")) / 2), ""];
        $result[] = [""];
        $result[] = ["", "", "TOTOTSARA Nestor" . str_repeat(" ", (strlen("Toamasina, le {$this->today}") - strlen("TOTOTSARA Nestor")) / 2), ""];

        $this->rowCount = count($result) + 6;

        foreach ($this->shouldBeMerged as $value) {
            Logger::log(array_reduce($value, fn($carry, $item) => $carry . ', ' . $item, ''));
        }

        return $result;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7.43,
            'B' => 10.57,
            'C' => 57.43,
            'D' => 11.71
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                Logger::log($this->name . ': ' . $this->rowCount);

                $delegate = $event->sheet->getDelegate();
                $delegate->getParent()->getDefaultStyle()->getFont()->applyFromArray(array(
                    'name' => 'Times New Roman',
                    'size' => 12,
                    'align' => 'center'
                ));

                for ($i = 0; $i < $this->rowCount; $i++) {
                    $delegate->getRowDimension($i)->setRowHeight(29, 'px');
                }


                /**
                 * Text: 'Emploi du temps'
                 */
                $delegate->getCell('A6')->getAppliedStyle()->getAlignment()->setHorizontal('center');
                // $delegate->getCell('A6')->getAppliedStyle()->getFont()->setBold(true);
                // $delegate->getCell('A6')->getAppliedStyle()->getFont()->setSize(14);
                // $delegate->getCell('A6')->getAppliedStyle()->getFont()->setUnderline(true);
                $delegate->mergeCells('A6:D6');


                $borderTopStyle = ['top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]];
                $delegate->getCell('B6')->getAppliedStyle()->applyFromArray(['borders' => $borderTopStyle]);
                $delegate->getCell('C6')->getAppliedStyle()->applyFromArray(['borders' => $borderTopStyle]);
                $delegate->getCell('D6')->getAppliedStyle()->applyFromArray(['borders' => $borderTopStyle]);
                $delegate->getCell('A6')->getAppliedStyle()->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Times New Roman',
                            'bold' => true,
                            'size' => 14,
                            'underline' => StyleFont::UNDERLINE_SINGLE,
                        ],
                        'borders' => $borderTopStyle,
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]
                );

                $delegate->mergeCells('A7:D7');
                $delegate->getCell('A7')->getAppliedStyle()->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Times New Roman',
                            'bold' => false,
                            'italic' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER
                        ]
                    ]
                );

                $delegate->getCell("A8")->getAppliedStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $delegate->getCell("D8")->getAppliedStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $delegate->getCell("A9")->getAppliedStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $delegate->getCell("D9")->getAppliedStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $abcd = ["A", "B", "C", "D"];
                for ($i = 0; $i < 4; $i++) {
                    $delegate->getCell("{$abcd[$i]}10")->getAppliedStyle()->applyFromArray(
                        [
                            'font' => [
                                'name' => 'Times New Roman',
                                'bold' => true,
                                'size' => 12,
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'fill' => array(
                                'fillType' => 'solid',
                                'color' => array('rgb' => 'F2F2F2')
                            ),
                            'borders' => [
                                'top' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'left' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'bottom' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ]
                            ]
                        ]
                    );
                }

                foreach ($this->shouldBeMerged as $shouldMerge) {
                    Logger::log($shouldMerge[0] . ':' . $shouldMerge[1]);
                    $delegate->mergeCells($shouldMerge[0] . ':' . $shouldMerge[1]);
                }

                for ($i = 11; $i < $this->rowCount - 5; $i++) {
                    for ($j = 0; $j < 4; $j++) {
                        $delegate->getCell("{$abcd[$j]}{$i}")->getAppliedStyle()->applyFromArray(array(
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'top' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'left' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'bottom' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ],
                                'right' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '444444'
                                    ]
                                ]
                            ]
                        ));
                    }
                }

                $delegate->getCell("A" . ($this->rowCount - 5))->getAppliedStyle()->applyFromArray(array(
                    'font' => [
                        'size' => 10,
                        'italic' => true,
                        'font-weight' => 'lighter'
                    ]
                ));


                for ($i = 1; $i <= 5; $i++) {
                    $delegate->getCell("C" . ($this->rowCount - $i))->getAppliedStyle()->applyFromArray(array(
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ]
                    ));
                }
            }
        ];
    }
}

<?php

class Rating
{
  public $filePath = './../../reit.csv';
  public $rait = [];
  public $users = [];
  public $filter = ['Андрей', 'Роман', 'Гена', 'Кавун', 'Олег'];
  public $rowCount = 1;
  public $template = [
   'name' => '',
   'wins' => 0,
   'loss' => 0,
   'downtrodden' => 0,
   'missed' => 0,
   'percent_wins' => 0,
   'percent_diff_downtrodden' => 0,
   'miss_plus_down' => 0
  ];
  
  public function getRating()
  {
    if (($handle = fopen($this->filePath, "r")) !== false) {
      while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $num = count($data);
        $this->rowCount++;
        $tmpAr = [];
        for ($c = 0; $c < $num; $c++) {
          $tmpAr[] = $data[$c];
        }
        $this->rait[] = $tmpAr;
      }
      unset($this->rait[0]);
      
      foreach ($this->rait as $item => $value) {
        
        if (!in_array($value[1], $this->filter) || !in_array($value[2], $this->filter)) {
          continue;
        }
        
        if (!isset($this->users[$value[1]])) {
          $this->template['name'] = $value[1];
          $this->users[$value[1]] = $this->template;
        }
        
        $this->users[$value[1]]['wins']++;
        $this->users[$value[1]]['downtrodden'] += 10;
        
        if (!isset($this->users[$value[2]])) {
          $this->template['name'] = $value[2];
          $this->users[$value[2]] = $this->template;
        }
        
        $this->users[$value[2]]['loss']++;
        
        $this->users[$value[2]]['downtrodden'] += $value[4];
        $this->users[$value[2]]['missed'] += 10;
        $this->users[$value[1]]['missed'] += $value[4];
        
        $allGame1 = $this->users[$value[1]]['loss'] + $this->users[$value[1]]['wins'];
        $allGame2 = $this->users[$value[2]]['loss'] + $this->users[$value[2]]['wins'];
        
        $this->users[$value[1]]['all_game'] = $allGame1;
        $this->users[$value[2]]['all_game'] = $allGame2;
        
        $this->users[$value[1]]['diff'] = $this->users[$value[1]]['downtrodden'] - $this->users[$value[1]]['missed'];
        $this->users[$value[2]]['diff'] = $this->users[$value[2]]['downtrodden'] - $this->users[$value[2]]['missed'];
        
        $this->users[$value[1]]['miss_plus_down'] = $this->users[$value[1]]['downtrodden'] + $this->users[$value[1]]['missed'];
        $this->users[$value[2]]['miss_plus_down'] = $this->users[$value[2]]['downtrodden'] + $this->users[$value[2]]['missed'];
        
        $this->users[$value[1]]['percent_diff_downtrodden'] = round(($this->users[$value[1]]['downtrodden'] / $this->users[$value[1]]['miss_plus_down']) * 100,
         2);
        $this->users[$value[2]]['percent_diff_downtrodden'] = round(($this->users[$value[2]]['downtrodden'] / $this->users[$value[2]]['miss_plus_down']) * 100,
         2);
        
        $this->users[$value[1]]['percent_wins'] = round(($this->users[$value[1]]['wins'] / $allGame1) * 100, 2);
        $this->users[$value[2]]['percent_wins'] = round(($this->users[$value[2]]['wins'] / $allGame2) * 100, 2);
      }
      
      fclose($handle);
      
      return $this->users;
    }
  }
}

$rait = new Rating();
$users = $rait->getRating();
echo '<pre>';
var_dump($users);
echo '</pre>';
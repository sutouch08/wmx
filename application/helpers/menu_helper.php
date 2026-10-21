<?php

//--- check menu level 1 that open or not
function isOpenMenu($menu, $code)
{
  return $menu === $code ? 'open' : '';
}


function isActiveOpenMenu($menu, $code)
{
  return $menu === $code ? 'active open' : '';
}


function active_menu($menu, $code)
{
  return $menu === $code ? 'active' : '';
}


function side_menu($menu_code, $code, $url, $name)
{
  $menu = '';
  $menu .= '<li class="'.active_menu($menu_code, $code).'">';
  $menu .= '<a href="'.base_url().$url.'">';
  $menu .= '<span class="menu-text">'.$name.'</span>';
  $menu .= '</a>';
  $menu .= '</li>';

  return $menu;
}

function select_menu_group($menu_groups, $se = '')
{
  $html = '';
  if(!empty($menu_groups))
  {
    foreach($menu_groups as $mg)
    {
      $selected = $se === $mg->code ? 'selected' : '';
      $html .= '<option value="'.$mg->code.'" '.$selected.'>'.$mg->name.'</option>';
    }
  }

  return $html;
}

function select_menu_sub_group($menu_sub_groups, $se = '')
{
  $html = '';
  if(!empty($menu_sub_groups))
  {
    foreach($menu_sub_groups as $sg)
    {
      $selected = $se === $sg->code ? 'selected' : '';
      $html .= '<option value="'.$sg->code.'" '.$selected.'>'.$sg->name.'</option>';
    }
  }

  return $html;
}

 ?>

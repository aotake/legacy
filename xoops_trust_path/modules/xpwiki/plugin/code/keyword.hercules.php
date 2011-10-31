<?php
/**
 * Hercules キーワード定義ファイル
 */

// コメント定義
$switchHash['/'] = $this->cont['PLUGIN_CODE_COMMENT'];        //  コメントは /* から */ までと // から改行まで
$code_comment = Array(
	'/' => Array(
				 Array('/^\/\*/', '*/', 2),
				 Array('/^\/\//', "\n", 1),
	)
);

// アウトライン用
if($mkoutline){
  $switchHash['{'] = $this->cont['PLUGIN_CODE_BLOCK_START'];
  $switchHash['}'] = $this->cont['PLUGIN_CODE_BLOCK_END'];
}

$code_css = Array(
  'operator',		// オペレータ関数
  'identifier',	// その他の識別子
  'pragma',		// module, import と pragma
  'system',		// 処理系組み込みの奴 __stdcall とか
  );

$code_keyword = Array(
  'header' => 2,
  'assign_property' => 2,
  'alias' => 2,
  'assign' => 2,
  'options' => 2,
  'preprocess_options' => 2,
  'explode_options' => 2,
  'technology_options' => 2,
  'drc_options' => 2,
  'database_options' => 2,
  'text_options' => 2,
  'lpe_options' => 2,
  'evaccess_options' => 2,
  'check_point' => 2,
  'compare_group' => 2,
  'environment' => 2,
  'grid_check' => 2,
  'include' => 2,
  'layer_stats' => 2,
  'load_group' => 2,
  'restart' => 2,
  'run_only' => 2,
  'self_intersect' => 2,
  'set' => 2,
  'snap' => 2,
  'system' => 2,
  'variable' => 2,
  'waiver' => 2,
  'attach_property' => 2,
  'boolean' => 2,
  'cell_extent' => 2,
  'common_hierarchy' => 2,
  'connection_points' => 2,
  'copy' => 2,
  'data_filter' => 2,
  'alternate' => 2,
  'delete' => 2,
  'explode' => 2,
  'explode_all' => 2,
  'fill_pattern' => 2,
  'find_net' => 2,
  'flatten' => 2,
  'level' => 2,
  'negate' => 2,
  'polygon_features' => 2,
  'push' => 2,
  'rectangles' => 2,
  'relocate' => 2,
  'remove_overlap' => 2,
  'reverse' => 2,
  'select' => 2,
  'select_cell' => 2,
  'select_contains' => 2,
  'select_edge' => 2,
  'select_net' => 2,
  'size' => 2,
  'text_polygon' => 2,
  'text_property' => 2,
  'vertex' => 2,
  'area' => 2,
  'cut' => 2,
  'density' => 2,
  'enclose' => 2,
  'external' => 2,
  'inside_edge' => 2,
  'internal' => 2,
  'notch' => 2,
  'vectorize' => 2,
  'center_to_center' => 2,
  'length' => 2,
  'mask_align' => 2,
  'moscheck' => 2,
  'rescheck' => 2,
  'analysis' => 2,
  'buildsub' => 2,
  'init_lpe_db' => 2,
  'capacitor' => 2,
  'device' => 2,
  'gendev' => 2,
  'nmos' => 2,
  'pmos' => 2,
  'diode' => 2,
  'npn' => 2,
  'pnp' => 2,
  'resistor' => 2,
  'set_param' => 2,
  'save_property' => 2,
  'connect' => 2,
  'disconnect' => 2,
  'text' => 2,
  'text_boolean' => 2,
  'replace_text' => 2,
  'create_ports' => 2,
  'label' => 2,
  'graphics' => 2,
  'save_netlist_database' => 2,
  'lpe_stats' => 2,
  'netlist' => 2,
  'spice' => 2,
  'graphics_property' => 2,
  'graphics_netlist' => 2,
  'write_milkyway' => 2,
  'multi_rule_enclose' => 2,
  'if' => 2,
  'error_property' => 2,
  'equate' => 2,
  'compare' => 2,
  'antenna_fix' => 2,
  'c_thru' => 2,
  'dev_connect_check' => 2,
  'dev_net_count' => 2,
  'device_count' => 2,
  'net_filter' => 2,
  'net_path_check' => 2,
  'ratio' => 2,
  'process_text_opens' => 2,
  'black_box_file' => 2,
  'block' => 2,
  'compare_dir' => 2,
  'equivalence' => 2,
  'format' => 2,
  'gdsin_dir' => 2,
  'group_dir' => 2,
  'group_dir_usage' => 2,
  'inlib' => 2,
  'layout_path' => 2,
  'outlib' => 2,
  'output_format' => 2,
  'output_layout_path' => 2,
  'schematic' => 2,
  'schematic_format' => 2,
  'scheme_file' => 2,
  'output_block' => 2,
  'else' => 2,
  'and' => 2,
  'or' => 2,
  'not' => 2,
  'xor' => 2,
  'andoverlap' => 2,
  'inside' => 2,
  'outside' => 2,
  'by' => 2,
  'to' => 2,
  'with' => 2,
  'connected' => 2,
  'connected_all' => 2,
  'texted_with' => 2,
  'texted' => 2,
  'by_property' => 2,
  'cutting' => 2,
  'edge_touch' => 2,
  'enclosing' => 2,
  'inside' => 2,
  'inside_hole' => 2,
  'interact' => 2,
  'touching' => 2,
  'vertex' => 2,

  );?>
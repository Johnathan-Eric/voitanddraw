<?php
 goto Tc31N; vhw__: load()->classs("\x63\x6f\x75\160\157\156"); goto YMFLe; Tc31N: defined("\x49\116\137\x49\101") or die("\x41\x63\x63\x65\x73\163\40\x44\145\x6e\x69\145\144"); goto vhw__; YMFLe: class WnCoupon extends coupon { public function BuildCardExt($id, $openid = '', $type = "\x63\157\x75\x70\157\x6e") { goto ho73M; VWx1m: $time = TIMESTAMP; goto tPeNe; npHW3: h5fRh: goto VWx1m; tPeNe: $sign = array($card_id, $time); goto adK9l; HhU2Q: rgE9H: goto arFOv; xqjH_: return array("\143\141\x72\x64\x5f\x69\x64" => $card_id, "\x63\x61\x72\144\x5f\145\170\164" => $cardExt); goto fH5qL; w6NBw: if ($iGcxN) { goto rgE9H; } goto tSzMU; adK9l: $signature = $this->SignatureCard($sign); goto AnpKu; w0nhm: return error(-1, "\345\x8d\241\xe5\210\xb8\x69\x64\xe4\270\215\xe5\x90\x88\346\263\x95"); goto npHW3; VO5Fq: if ($OpUkq) { goto h5fRh; } goto w0nhm; AnpKu: $iGcxN = !is_error($signature); goto w6NBw; I9dNk: $cardExt = json_encode($cardExt); goto xqjH_; tSzMU: return $signature; goto HhU2Q; ho73M: $card_id = pdo_fetchcolumn("\123\105\114\x45\x43\124\x20\143\x61\x72\x64\x5f\151\144\40\x46\x52\117\115\40" . tablename("\163\164\157\162\x65\x78\137\x63\157\165\160\x6f\156") . "\40\127\x48\x45\x52\x45\x20\151\144\40\75\40\x3a\151\x64", array("\72\x69\x64" => $id)); goto OhGw6; arFOv: $cardExt = array("\164\151\155\x65\163\x74\141\x6d\x70" => $time, "\163\151\147\156\141\x74\165\162\145" => $signature); goto I9dNk; OhGw6: $OpUkq = !empty($card_id); goto VO5Fq; fH5qL: } }
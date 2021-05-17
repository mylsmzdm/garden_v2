<?php

/**
 * Created by PhpStorm.
 * User: wind
 * Date: 15/10/14
 * Time: 下午3:50
 */
Class SmzdmLinkCpsConfig
{

    const SMZDM_GO_DOMAIN = "https://go.smzdm.com/";

    static $redis = array("data" => array(
        'write' => array("hostname" => "link_data_redis_m01", "port" => 6379, "timeout" => 5),
        'read' =>
            array(
                array("hostname" => "link_data_redis_s01", "port" => 6379, "timeout" => 5),
                array("hostname" => "link_data_redis_s01", "port" => 6379, "timeout" => 5)
            )
    ), "cache" => array(
        'write' => array("hostname" => "link_data_redis_m01", "port" => 6379, "timeout" => 5),
        'read' =>
            array(
                array("hostname" => "link_data_redis_s01", "port" => 6379, "timeout" => 5),
                array("hostname" => "link_data_redis_s01", "port" => 6379, "timeout" => 5)
            )
    ),
    );

    static $valid_tlds = array(
        'ab.ca' => 1, 'bc.ca' => 1, 'mb.ca' => 1, 'nb.ca' => 1, 'nf.ca' => 1, 'nl.ca' => 1, 'ns.ca' => 1, 'nt.ca' => 1, 'nu.ca' => 1,
        'pe.ca' => 1, 'qc.ca' => 1, 'sk.ca' => 1, 'yk.ca' => 1, 'com.cd' => 1, 'net.cd' => 1, 'org.cd' => 1, 'com.ch' => 1,
        'org.ch' => 1, 'gov.ch' => 1, 'co.ck' => 1, 'ac.cn' => 1, 'com.cn' => 1, 'edu.cn' => 1, 'gov.cn' => 1, 'net.cn' => 1,
        'ah.cn' => 1, 'bj.cn' => 1, 'cq.cn' => 1, 'fj.cn' => 1, 'gd.cn' => 1, 'gs.cn' => 1, 'gz.cn' => 1, 'gx.cn' => 1, 'ha.cn' => 1,
        'he.cn' => 1, 'hi.cn' => 1, 'hl.cn' => 1, 'hn.cn' => 1, 'jl.cn' => 1, 'js.cn' => 1, 'jx.cn' => 1, 'ln.cn' => 1, 'nm.cn' => 1,
        'qh.cn' => 1, 'sc.cn' => 1, 'sd.cn' => 1, 'sh.cn' => 1, 'sn.cn' => 1, 'sx.cn' => 1, 'tj.cn' => 1, 'xj.cn' => 1, 'xz.cn' => 1,
        'zj.cn' => 1, 'com.co' => 1, 'edu.co' => 1, 'org.co' => 1, 'gov.co' => 1, 'mil.co' => 1, 'net.co' => 1, 'nom.co' => 1,
        'edu.cu' => 1, 'org.cu' => 1, 'net.cu' => 1, 'gov.cu' => 1, 'inf.cu' => 1, 'gov.cx' => 1, 'edu.do' => 1, 'gov.do' => 1,
        'com.do' => 1, 'org.do' => 1, 'sld.do' => 1, 'web.do' => 1, 'net.do' => 1, 'mil.do' => 1, 'art.do' => 1, 'com.dz' => 1,
        'net.dz' => 1, 'gov.dz' => 1, 'edu.dz' => 1, 'asso.dz' => 1, 'pol.dz' => 1, 'art.dz' => 1, 'com.ec' => 1, 'info.ec' => 1,
        'fin.ec' => 1, 'med.ec' => 1, 'pro.ec' => 1, 'org.ec' => 1, 'edu.ec' => 1, 'gov.ec' => 1, 'mil.ec' => 1, 'com.ee' => 1,
        'fie.ee' => 1, 'pri.ee' => 1, 'eun.eg' => 1, 'edu.eg' => 1, 'sci.eg' => 1, 'gov.eg' => 1, 'com.eg' => 1, 'org.eg' => 1,
        'mil.eg' => 1, 'com.es' => 1, 'nom.es' => 1, 'org.es' => 1, 'gob.es' => 1, 'edu.es' => 1, 'com.et' => 1, 'gov.et' => 1,
        'edu.et' => 1, 'net.et' => 1, 'biz.et' => 1, 'name.et' => 1, 'info.et' => 1, 'co.fk' => 1, 'org.fk' => 1, 'gov.fk' => 1,
        'nom.fk' => 1, 'net.fk' => 1, 'tm.fr' => 1, 'asso.fr' => 1, 'nom.fr' => 1, 'prd.fr' => 1, 'presse.fr' => 1,
        'gouv.fr' => 1, 'com.ge' => 1, 'edu.ge' => 1, 'gov.ge' => 1, 'org.ge' => 1, 'mil.ge' => 1, 'net.ge' => 1, 'pvt.ge' => 1,
        'net.gg' => 1, 'org.gg' => 1, 'com.gi' => 1, 'ltd.gi' => 1, 'gov.gi' => 1, 'mod.gi' => 1, 'edu.gi' => 1, 'org.gi' => 1,
        'ac.gn' => 1, 'gov.gn' => 1, 'org.gn' => 1, 'net.gn' => 1, 'com.gr' => 1, 'edu.gr' => 1, 'net.gr' => 1, 'org.gr' => 1,
        'com.hk' => 1, 'edu.hk' => 1, 'gov.hk' => 1, 'idv.hk' => 1, 'net.hk' => 1, 'org.hk' => 1, 'com.hn' => 1, 'edu.hn' => 1,
        'net.hn' => 1, 'mil.hn' => 1, 'gob.hn' => 1, 'iz.hr' => 1, 'from.hr' => 1, 'name.hr' => 1, 'com.hr' => 1, 'com.ht' => 1,
        'firm.ht' => 1, 'shop.ht' => 1, 'info.ht' => 1, 'pro.ht' => 1, 'adult.ht' => 1, 'org.ht' => 1, 'art.ht' => 1,
        'rel.ht' => 1, 'asso.ht' => 1, 'perso.ht' => 1, 'coop.ht' => 1, 'med.ht' => 1, 'edu.ht' => 1, 'gouv.ht' => 1,
        'co.in' => 1, 'firm.in' => 1, 'net.in' => 1, 'org.in' => 1, 'gen.in' => 1, 'ind.in' => 1, 'nic.in' => 1, 'ac.in' => 1,
        'res.in' => 1, 'gov.in' => 1, 'mil.in' => 1, 'ac.ir' => 1, 'co.ir' => 1, 'gov.ir' => 1, 'net.ir' => 1, 'org.ir' => 1,
        'gov.it' => 1, 'co.je' => 1, 'net.je' => 1, 'org.je' => 1, 'edu.jm' => 1, 'gov.jm' => 1, 'com.jm' => 1, 'net.jm' => 1,
        'org.jo' => 1, 'net.jo' => 1, 'edu.jo' => 1, 'gov.jo' => 1, 'mil.jo' => 1, 'co.kr' => 1, 'or.kr' => 1, 'com.kw' => 1,
        'gov.kw' => 1, 'net.kw' => 1, 'org.kw' => 1, 'mil.kw' => 1, 'edu.ky' => 1, 'gov.ky' => 1, 'com.ky' => 1, 'org.ky' => 1,
        'org.kz' => 1, 'edu.kz' => 1, 'net.kz' => 1, 'gov.kz' => 1, 'mil.kz' => 1, 'com.kz' => 1, 'com.li' => 1, 'net.li' => 1,
        'gov.li' => 1, 'gov.lk' => 1, 'sch.lk' => 1, 'net.lk' => 1, 'int.lk' => 1, 'com.lk' => 1, 'org.lk' => 1, 'edu.lk' => 1,
        'soc.lk' => 1, 'web.lk' => 1, 'ltd.lk' => 1, 'assn.lk' => 1, 'grp.lk' => 1, 'hotel.lk' => 1, 'com.lr' => 1,
        'gov.lr' => 1, 'org.lr' => 1, 'net.lr' => 1, 'org.ls' => 1, 'co.ls' => 1, 'gov.lt' => 1, 'mil.lt' => 1, 'gov.lu' => 1,
        'org.lu' => 1, 'net.lu' => 1, 'com.lv' => 1, 'edu.lv' => 1, 'gov.lv' => 1, 'org.lv' => 1, 'mil.lv' => 1, 'id.lv' => 1,
        'asn.lv' => 1, 'conf.lv' => 1, 'com.ly' => 1, 'net.ly' => 1, 'gov.ly' => 1, 'plc.ly' => 1, 'edu.ly' => 1, 'sch.ly' => 1,
        'org.ly' => 1, 'id.ly' => 1, 'co.ma' => 1, 'net.ma' => 1, 'gov.ma' => 1, 'org.ma' => 1, 'tm.mc' => 1, 'asso.mc' => 1,
        'nom.mg' => 1, 'gov.mg' => 1, 'prd.mg' => 1, 'tm.mg' => 1, 'com.mg' => 1, 'edu.mg' => 1, 'mil.mg' => 1, 'com.mk' => 1,
        'com.mo' => 1, 'net.mo' => 1, 'org.mo' => 1, 'edu.mo' => 1, 'gov.mo' => 1, 'org.mt' => 1, 'com.mt' => 1, 'gov.mt' => 1,
        'net.mt' => 1, 'com.mu' => 1, 'co.mu' => 1, 'aero.mv' => 1, 'biz.mv' => 1, 'com.mv' => 1, 'coop.mv' => 1, 'edu.mv' => 1,
        'info.mv' => 1, 'int.mv' => 1, 'mil.mv' => 1, 'museum.mv' => 1, 'name.mv' => 1, 'net.mv' => 1, 'org.mv' => 1,
        'com.mx' => 1, 'net.mx' => 1, 'org.mx' => 1, 'edu.mx' => 1, 'gob.mx' => 1, 'com.my' => 1, 'net.my' => 1, 'org.my' => 1,
        'edu.my' => 1, 'mil.my' => 1, 'name.my' => 1, 'edu.ng' => 1, 'com.ng' => 1, 'gov.ng' => 1, 'org.ng' => 1, 'net.ng' => 1,
        'com.ni' => 1, 'edu.ni' => 1, 'org.ni' => 1, 'nom.ni' => 1, 'net.ni' => 1, 'gov.nr' => 1, 'edu.nr' => 1, 'biz.nr' => 1,
        'com.nr' => 1, 'net.nr' => 1, 'ac.nz' => 1, 'co.nz' => 1, 'cri.nz' => 1, 'gen.nz' => 1, 'geek.nz' => 1, 'govt.nz' => 1,
        'maori.nz' => 1, 'mil.nz' => 1, 'net.nz' => 1, 'org.nz' => 1, 'school.nz' => 1, 'com.pf' => 1, 'org.pf' => 1,
        'com.pg' => 1, 'net.pg' => 1, 'com.ph' => 1, 'gov.ph' => 1, 'com.pk' => 1, 'net.pk' => 1, 'edu.pk' => 1, 'org.pk' => 1,
        'biz.pk' => 1, 'web.pk' => 1, 'gov.pk' => 1, 'gob.pk' => 1, 'gok.pk' => 1, 'gon.pk' => 1, 'gop.pk' => 1, 'gos.pk' => 1,
        'biz.pl' => 1, 'net.pl' => 1, 'art.pl' => 1, 'edu.pl' => 1, 'org.pl' => 1, 'ngo.pl' => 1, 'gov.pl' => 1, 'info.pl' => 1,
        'waw.pl' => 1, 'warszawa.pl' => 1, 'wroc.pl' => 1, 'wroclaw.pl' => 1, 'krakow.pl' => 1, 'poznan.pl' => 1,
        'gda.pl' => 1, 'gdansk.pl' => 1, 'slupsk.pl' => 1, 'szczecin.pl' => 1, 'lublin.pl' => 1, 'bialystok.pl' => 1,
        'torun.pl' => 1, 'biz.pr' => 1, 'com.pr' => 1, 'edu.pr' => 1, 'gov.pr' => 1, 'info.pr' => 1, 'isla.pr' => 1,
        'net.pr' => 1, 'org.pr' => 1, 'pro.pr' => 1, 'edu.ps' => 1, 'gov.ps' => 1, 'sec.ps' => 1, 'plo.ps' => 1, 'com.ps' => 1,
        'net.ps' => 1, 'com.pt' => 1, 'edu.pt' => 1, 'gov.pt' => 1, 'int.pt' => 1, 'net.pt' => 1, 'nome.pt' => 1, 'org.pt' => 1,
        'net.py' => 1, 'org.py' => 1, 'gov.py' => 1, 'edu.py' => 1, 'com.py' => 1, 'com.ru' => 1, 'net.ru' => 1, 'org.ru' => 1,
        'msk.ru' => 1, 'int.ru' => 1, 'ac.ru' => 1, 'gov.rw' => 1, 'net.rw' => 1, 'edu.rw' => 1, 'ac.rw' => 1, 'com.rw' => 1,
        'int.rw' => 1, 'mil.rw' => 1, 'gouv.rw' => 1, 'com.sa' => 1, 'edu.sa' => 1, 'sch.sa' => 1, 'med.sa' => 1, 'gov.sa' => 1,
        'org.sa' => 1, 'pub.sa' => 1, 'com.sb' => 1, 'gov.sb' => 1, 'net.sb' => 1, 'edu.sb' => 1, 'com.sc' => 1, 'gov.sc' => 1,
        'org.sc' => 1, 'edu.sc' => 1, 'com.sd' => 1, 'net.sd' => 1, 'org.sd' => 1, 'edu.sd' => 1, 'med.sd' => 1, 'tv.sd' => 1,
        'info.sd' => 1, 'org.se' => 1, 'pp.se' => 1, 'tm.se' => 1, 'parti.se' => 1, 'press.se' => 1, 'ab.se' => 1, 'c.se' => 1,
        'e.se' => 1, 'f.se' => 1, 'g.se' => 1, 'h.se' => 1, 'i.se' => 1, 'k.se' => 1, 'm.se' => 1, 'n.se' => 1, 'o.se' => 1, 's.se' => 1,
        'u.se' => 1, 'w.se' => 1, 'x.se' => 1, 'y.se' => 1, 'z.se' => 1, 'ac.se' => 1, 'bd.se' => 1, 'com.sg' => 1, 'net.sg' => 1,
        'gov.sg' => 1, 'edu.sg' => 1, 'per.sg' => 1, 'idn.sg' => 1, 'edu.sv' => 1, 'com.sv' => 1, 'gob.sv' => 1, 'org.sv' => 1,
        'gov.sy' => 1, 'com.sy' => 1, 'net.sy' => 1, 'ac.th' => 1, 'co.th' => 1, 'in.th' => 1, 'go.th' => 1, 'mi.th' => 1,
        'net.th' => 1, 'ac.tj' => 1, 'biz.tj' => 1, 'com.tj' => 1, 'co.tj' => 1, 'edu.tj' => 1, 'int.tj' => 1, 'name.tj' => 1,
        'org.tj' => 1, 'web.tj' => 1, 'gov.tj' => 1, 'go.tj' => 1, 'mil.tj' => 1, 'com.tn' => 1, 'intl.tn' => 1, 'gov.tn' => 1,
        'ind.tn' => 1, 'nat.tn' => 1, 'tourism.tn' => 1, 'info.tn' => 1, 'ens.tn' => 1, 'fin.tn' => 1, 'net.tn' => 1,
        'gov.tp' => 1, 'com.tr' => 1, 'info.tr' => 1, 'biz.tr' => 1, 'net.tr' => 1, 'org.tr' => 1, 'web.tr' => 1, 'gen.tr' => 1,
        'dr.tr' => 1, 'bbs.tr' => 1, 'name.tr' => 1, 'tel.tr' => 1, 'gov.tr' => 1, 'bel.tr' => 1, 'pol.tr' => 1, 'mil.tr' => 1,
        'edu.tr' => 1, 'co.tt' => 1, 'com.tt' => 1, 'org.tt' => 1, 'net.tt' => 1, 'biz.tt' => 1, 'info.tt' => 1, 'pro.tt' => 1,
        'edu.tt' => 1, 'gov.tt' => 1, 'gov.tv' => 1, 'edu.tw' => 1, 'gov.tw' => 1, 'mil.tw' => 1, 'com.tw' => 1, 'net.tw' => 1,
        'idv.tw' => 1, 'game.tw' => 1, 'ebiz.tw' => 1, 'club.tw' => 1, 'co.tz' => 1, 'ac.tz' => 1, 'go.tz' => 1, 'or.tz' => 1,
        'com.ua' => 1, 'gov.ua' => 1, 'net.ua' => 1, 'edu.ua' => 1, 'org.ua' => 1, 'cherkassy.ua' => 1, 'ck.ua' => 1,
        'cn.ua' => 1, 'chernovtsy.ua' => 1, 'cv.ua' => 1, 'crimea.ua' => 1, 'dnepropetrovsk.ua' => 1, 'dp.ua' => 1,
        'dn.ua' => 1, 'if.ua' => 1, 'kharkov.ua' => 1, 'kh.ua' => 1, 'kherson.ua' => 1, 'ks.ua' => 1,
        'km.ua' => 1, 'kiev.ua' => 1, 'kv.ua' => 1, 'kirovograd.ua' => 1, 'kr.ua' => 1, 'lugansk.ua' => 1, 'lg.ua' => 1,
        'lviv.ua' => 1, 'nikolaev.ua' => 1, 'mk.ua' => 1, 'odessa.ua' => 1, 'od.ua' => 1, 'poltava.ua' => 1, 'pl.ua' => 1,
        'rv.ua' => 1, 'sebastopol.ua' => 1, 'sumy.ua' => 1, 'ternopil.ua' => 1, 'te.ua' => 1, 'uzhgorod.ua' => 1,
        'vn.ua' => 1, 'zaporizhzhe.ua' => 1, 'zp.ua' => 1, 'zhitomir.ua' => 1, 'zt.ua' => 1, 'co.ug' => 1, 'ac.ug' => 1,
        'go.ug' => 1, 'ne.ug' => 1, 'or.ug' => 1, 'ac.uk' => 1, 'co.uk' => 1, 'gov.uk' => 1, 'ltd.uk' => 1, 'me.uk' => 1,
        'mod.uk' => 1, 'net.uk' => 1, 'nic.uk' => 1, 'nhs.uk' => 1, 'org.uk' => 1, 'plc.uk' => 1, 'police.uk' => 1, 'bl.uk' => 1,
        'jet.uk' => 1, 'nel.uk' => 1, 'nls.uk' => 1, 'parliament.uk' => 1, 'sch.uk' => 1, 'ak.us' => 1, 'al.us' => 1,
        'az.us' => 1, 'ca.us' => 1, 'co.us' => 1, 'ct.us' => 1, 'dc.us' => 1, 'de.us' => 1, 'dni.us' => 1, 'fed.us' => 1,
        'ga.us' => 1, 'hi.us' => 1, 'ia.us' => 1, 'id.us' => 1, 'il.us' => 1, 'in.us' => 1, 'isa.us' => 1, 'kids.us' => 1,
        'ky.us' => 1, 'la.us' => 1, 'ma.us' => 1, 'md.us' => 1, 'me.us' => 1, 'mi.us' => 1, 'mn.us' => 1, 'mo.us' => 1, 'ms.us' => 1,
        'nc.us' => 1, 'nd.us' => 1, 'ne.us' => 1, 'nh.us' => 1, 'nj.us' => 1, 'nm.us' => 1, 'nsn.us' => 1, 'nv.us' => 1, 'ny.us' => 1,
        'ok.us' => 1, 'or.us' => 1, 'pa.us' => 1, 'ri.us' => 1, 'sc.us' => 1, 'sd.us' => 1, 'tn.us' => 1, 'tx.us' => 1, 'ut.us' => 1,
        'va.us' => 1, 'wa.us' => 1, 'wi.us' => 1, 'wv.us' => 1, 'wy.us' => 1, 'edu.uy' => 1, 'gub.uy' => 1, 'org.uy' => 1,
        'net.uy' => 1, 'mil.uy' => 1, 'com.ve' => 1, 'net.ve' => 1, 'org.ve' => 1, 'info.ve' => 1, 'co.ve' => 1, 'web.ve' => 1,
        'org.vi' => 1, 'edu.vi' => 1, 'gov.vi' => 1, 'com.vn' => 1, 'net.vn' => 1, 'org.vn' => 1, 'edu.vn' => 1, 'gov.vn' => 1,
        'ac.vn' => 1, 'biz.vn' => 1, 'info.vn' => 1, 'name.vn' => 1, 'pro.vn' => 1, 'health.vn' => 1, 'com.ye' => 1,
        'ac.yu' => 1, 'co.yu' => 1, 'org.yu' => 1, 'edu.yu' => 1, 'ac.za' => 1, 'city.za' => 1, 'co.za' => 1, 'edu.za' => 1,
        'law.za' => 1, 'mil.za' => 1, 'nom.za' => 1, 'org.za' => 1, 'school.za' => 1, 'alt.za' => 1, 'net.za' => 1,
        'tm.za' => 1, 'web.za' => 1, 'co.zm' => 1, 'org.zm' => 1, 'gov.zm' => 1, 'sch.zm' => 1, 'ac.zm' => 1, 'co.zw' => 1,
        'gov.zw' => 1, 'ac.zw' => 1, 'com.ac' => 1, 'edu.ac' => 1, 'gov.ac' => 1, 'net.ac' => 1, 'mil.ac' => 1, 'org.ac' => 1,
        'net.ae' => 1, 'co.ae' => 1, 'gov.ae' => 1, 'ac.ae' => 1, 'sch.ae' => 1, 'org.ae' => 1, 'mil.ae' => 1, 'pro.ae' => 1,
        'com.ag' => 1, 'org.ag' => 1, 'net.ag' => 1, 'co.ag' => 1, 'nom.ag' => 1, 'off.ai' => 1, 'com.ai' => 1, 'net.ai' => 1,
        'gov.al' => 1, 'edu.al' => 1, 'org.al' => 1, 'com.al' => 1, 'net.al' => 1, 'com.am' => 1, 'net.am' => 1, 'org.am' => 1,
        'net.ar' => 1, 'org.ar' => 1, 'e164.arpa' => 1, 'ip6.arpa' => 1, 'uri.arpa' => 1, 'urn.arpa' => 1, 'gv.at' => 1,
        'co.at' => 1, 'or.at' => 1, 'com.au' => 1, 'net.au' => 1, 'asn.au' => 1, 'org.au' => 1, 'id.au' => 1, 'csiro.au' => 1,
        'edu.au' => 1, 'com.aw' => 1, 'com.az' => 1, 'net.az' => 1, 'org.az' => 1, 'com.bb' => 1, 'edu.bb' => 1, 'gov.bb' => 1,
        'org.bb' => 1, 'com.bd' => 1, 'edu.bd' => 1, 'net.bd' => 1, 'gov.bd' => 1, 'org.bd' => 1, 'mil.be' => 1, 'ac.be' => 1,
        'com.bm' => 1, 'edu.bm' => 1, 'org.bm' => 1, 'gov.bm' => 1, 'net.bm' => 1, 'com.bn' => 1, 'edu.bn' => 1, 'org.bn' => 1,
        'com.bo' => 1, 'org.bo' => 1, 'net.bo' => 1, 'gov.bo' => 1, 'gob.bo' => 1, 'edu.bo' => 1, 'tv.bo' => 1, 'mil.bo' => 1,
        'agr.br' => 1, 'am.br' => 1, 'art.br' => 1, 'edu.br' => 1, 'com.br' => 1, 'coop.br' => 1, 'esp.br' => 1, 'far.br' => 1,
        'g12.br' => 1, 'gov.br' => 1, 'imb.br' => 1, 'ind.br' => 1, 'inf.br' => 1, 'mil.br' => 1, 'net.br' => 1, 'org.br' => 1,
        'rec.br' => 1, 'srv.br' => 1, 'tmp.br' => 1, 'tur.br' => 1, 'tv.br' => 1, 'etc.br' => 1, 'adm.br' => 1, 'adv.br' => 1,
        'ato.br' => 1, 'bio.br' => 1, 'bmd.br' => 1, 'cim.br' => 1, 'cng.br' => 1, 'cnt.br' => 1, 'ecn.br' => 1, 'eng.br' => 1,
        'fnd.br' => 1, 'fot.br' => 1, 'fst.br' => 1, 'ggf.br' => 1, 'jor.br' => 1, 'lel.br' => 1, 'mat.br' => 1, 'med.br' => 1,
        'not.br' => 1, 'ntr.br' => 1, 'odo.br' => 1, 'ppg.br' => 1, 'pro.br' => 1, 'psc.br' => 1, 'qsl.br' => 1, 'slg.br' => 1,
        'vet.br' => 1, 'zlg.br' => 1, 'dpn.br' => 1, 'nom.br' => 1, 'com.bs' => 1, 'net.bs' => 1, 'org.bs' => 1, 'com.bt' => 1,
        'gov.bt' => 1, 'net.bt' => 1, 'org.bt' => 1, 'co.bw' => 1, 'org.bw' => 1, 'gov.by' => 1, 'mil.by' => 1, 'ac.cr' => 1,
        'ed.cr' => 1, 'fi.cr' => 1, 'go.cr' => 1, 'or.cr' => 1, 'sa.cr' => 1, 'com.cy' => 1, 'biz.cy' => 1, 'info.cy' => 1,
        'pro.cy' => 1, 'net.cy' => 1, 'org.cy' => 1, 'name.cy' => 1, 'tm.cy' => 1, 'ac.cy' => 1, 'ekloges.cy' => 1,
        'parliament.cy' => 1, 'com.dm' => 1, 'net.dm' => 1, 'org.dm' => 1, 'edu.dm' => 1, 'gov.dm' => 1, 'biz.fj' => 1,
        'info.fj' => 1, 'name.fj' => 1, 'net.fj' => 1, 'org.fj' => 1, 'pro.fj' => 1, 'ac.fj' => 1, 'gov.fj' => 1, 'mil.fj' => 1,
        'com.gh' => 1, 'edu.gh' => 1, 'gov.gh' => 1, 'org.gh' => 1, 'mil.gh' => 1, 'co.hu' => 1, 'info.hu' => 1, 'org.hu' => 1,
        'sport.hu' => 1, 'tm.hu' => 1, '2000.hu' => 1, 'agrar.hu' => 1, 'bolt.hu' => 1, 'casino.hu' => 1, 'city.hu' => 1,
        'erotika.hu' => 1, 'film.hu' => 1, 'forum.hu' => 1, 'games.hu' => 1, 'hotel.hu' => 1, 'ingatlan.hu' => 1,
        'konyvelo.hu' => 1, 'lakas.hu' => 1, 'media.hu' => 1, 'news.hu' => 1, 'reklam.hu' => 1, 'sex.hu' => 1,
        'suli.hu' => 1, 'szex.hu' => 1, 'tozsde.hu' => 1, 'utazas.hu' => 1, 'video.hu' => 1, 'ac.id' => 1, 'co.id' => 1,
        'go.id' => 1, 'ac.il' => 1, 'co.il' => 1, 'org.il' => 1, 'net.il' => 1, 'k12.il' => 1, 'gov.il' => 1, 'muni.il' => 1,
        'co.im' => 1, 'net.im' => 1, 'gov.im' => 1, 'org.im' => 1, 'nic.im' => 1, 'ac.im' => 1, 'org.jm' => 1, 'ac.jp' => 1,
        'co.jp' => 1, 'ed.jp' => 1, 'go.jp' => 1, 'gr.jp' => 1, 'lg.jp' => 1, 'ne.jp' => 1, 'or.jp' => 1, 'hokkaido.jp' => 1,
        'iwate.jp' => 1, 'miyagi.jp' => 1, 'akita.jp' => 1, 'yamagata.jp' => 1, 'fukushima.jp' => 1, 'ibaraki.jp' => 1,
        'gunma.jp' => 1, 'saitama.jp' => 1, 'chiba.jp' => 1, 'tokyo.jp' => 1, 'kanagawa.jp' => 1, 'niigata.jp' => 1,
        'ishikawa.jp' => 1, 'fukui.jp' => 1, 'yamanashi.jp' => 1, 'nagano.jp' => 1, 'gifu.jp' => 1, 'shizuoka.jp' => 1,
        'mie.jp' => 1, 'shiga.jp' => 1, 'kyoto.jp' => 1, 'osaka.jp' => 1, 'hyogo.jp' => 1, 'nara.jp' => 1,
        'tottori.jp' => 1, 'shimane.jp' => 1, 'okayama.jp' => 1, 'hiroshima.jp' => 1, 'yamaguchi.jp' => 1,
        'kagawa.jp' => 1, 'ehime.jp' => 1, 'kochi.jp' => 1, 'fukuoka.jp' => 1, 'saga.jp' => 1, 'nagasaki.jp' => 1,
        'oita.jp' => 1, 'miyazaki.jp' => 1, 'kagoshima.jp' => 1, 'okinawa.jp' => 1, 'sapporo.jp' => 1,
        'yokohama.jp' => 1, 'kawasaki.jp' => 1, 'nagoya.jp' => 1, 'kobe.jp' => 1, 'kitakyushu.jp' => 1, 'per.kh' => 1,
        'edu.kh' => 1, 'gov.kh' => 1, 'mil.kh' => 1, 'net.kh' => 1, 'org.kh' => 1, 'net.lb' => 1, 'org.lb' => 1, 'gov.lb' => 1,
        'com.lb' => 1, 'com.lc' => 1, 'org.lc' => 1, 'edu.lc' => 1, 'gov.lc' => 1, 'army.mil' => 1, 'navy.mil' => 1,
        'music.mobi' => 1, 'ac.mw' => 1, 'co.mw' => 1, 'com.mw' => 1, 'coop.mw' => 1, 'edu.mw' => 1, 'gov.mw' => 1,
        'museum.mw' => 1, 'net.mw' => 1, 'org.mw' => 1, 'mil.no' => 1, 'stat.no' => 1, 'kommune.no' => 1, 'herad.no' => 1,
        'vgs.no' => 1, 'fhs.no' => 1, 'museum.no' => 1, 'fylkesbibl.no' => 1, 'folkebibl.no' => 1, 'idrett.no' => 1,
        'org.np' => 1, 'edu.np' => 1, 'net.np' => 1, 'gov.np' => 1, 'mil.np' => 1, 'org.nr' => 1, 'com.om' => 1, 'co.om' => 1,
        'ac.com' => 1, 'sch.om' => 1, 'gov.om' => 1, 'net.om' => 1, 'org.om' => 1, 'mil.om' => 1, 'museum.om' => 1,
        'pro.om' => 1, 'med.om' => 1, 'com.pa' => 1, 'ac.pa' => 1, 'sld.pa' => 1, 'gob.pa' => 1, 'edu.pa' => 1, 'org.pa' => 1,
        'abo.pa' => 1, 'ing.pa' => 1, 'med.pa' => 1, 'nom.pa' => 1, 'com.pe' => 1, 'org.pe' => 1, 'net.pe' => 1, 'edu.pe' => 1,
        'gob.pe' => 1, 'nom.pe' => 1, 'law.pro' => 1, 'med.pro' => 1, 'cpa.pro' => 1, 'vatican.va' => 1,
        'com.ar' => 1, 'edu.ar' => 1, 'gob.ar' => 1, 'gov.ar' => 1, 'int.ar' => 1, 'mil.ar' => 1, 'tur.ar' => 1,'cn.com' => 1
    );


    static $link_ignore_domain_list = array(
        'bq.com', 'smzdm.com'
    );
}



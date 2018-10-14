var G = G ||$|| window;
!function (t,w) {
	var e = t.ui || {};
	var img_dir = "/Public";
	if (e.dialog) return e.dialog;
	var n = {},
	i = document.documentElement,o = i.clientHeight,a = i.clientWidth,r = null,c = null,s = null,l = function (t) {return Array.isArray(t)},
	d = function (t) {document.body.appendChild(t)},
	u = {
		genDom: function (t, e, n) {
			if (t) if ('[object Object]' === Object.prototype.toString.call(t, null)) {
				t.type = t.type || 'loading',
				t.bg = t.bg || '',
				t.op = t.op || '',
				t.width = t.width || '250px',
				t.d_bg = t.d_bg || '#fff',
				t.d_op = t.d_op || '',
				t.wallCss = 'background:' + t.bg + ';opacity:' + t.op + ';filter:' + t.filter + ';',
				t.wrapCss = 'text-align:center;width:' + t.width + ';height:' + t.height + ';opacity:' + t.d_op + ';filter:' + t.d_filter + ';background: ' + t.d_bg + ';',
				e.style.cssText = t.wallCss,
				n.style.cssText = t.wrapCss;
				var i = '<div style=\'' + ('loading' === t.type ? 'padding:0px;' : 'padding: 0px 6%;') + '\'>' + u.genIcon(t) + u.genTitle(t) + u.genTip(t) + u.genButtons(t) + '</div>' + u.genClose(t);
				n.innerHTML = i
			} else '[object String]' === Object.prototype.toString.call(t, null) ? n.innerHTML = t : '[object HTMLDivElement]' === Object.prototype.toString.call(t, null) && (n.style.cssText = 'display:inline-block;width:250px;background-color:#fff;', t.style.display = 'inline-block', n.appendChild(t))
		},
		genIcon: function (t) {
			if (!t.icon) return '';
			var e = '';
			t.icon = t.icon || {
			};
			var n = t.icon.width || '8px',
			i = t.icon.height || '36px',
			o = t.icon.url || img_dir + '/images/i-plaint.png',
			a = '',
			r = '',
			c = '';
			return 'loading' === t.type ? (o = t.icon.url || img_dir+'/images/loading_2.gif', a = 'margin:36px 0 10px', c = 'display:inline-block;width:30px;height:30px;background:url(' + o + ') no-repeat;background-size:30px 30px;')  : (a = 'margin:24px 0 12px', c = 'display:inline-block;width:60px;height:60px;background-color:#f0f0f0;background-size:60px 60px;border-radius:50%;', r = '<span style="vertical-align:middle;display:inline-block;height:100%;"></span><img src=' + o + ' style="width:' + n + ';height:' + i + ';vertical-align:middle;" />'),
			e = '<p  style="' + a + '"><span style="' + c + '">' + r + '</span></p>'
		},
		genTitle: function (t) {
			t.title = t.title || {
			};
			var e = t.title.color || '#ff8a01',
			n = t.title.size || '1.6em';
			return '<p style="margin-bottom: 5px;color:' + e + ';font-size:' + n + ';">' + (t.title.txt || '') + '</p>'
		},
		genTip: function (t) {
			return t.title && t.title.txt ? (t.tip.color = t.tip.color || '#666', t.tip.size = t.tip.size || '1.3em')  : (t.tip.color = t.tip.color || '#333', t.tip.size = t.tip.size || '1.3em'),
			t.tip ? '<p style="' + ('loading' !== t.type ? 'text-align:center;' : '') + 'line-height:1.6em;color:' + t.tip.color + ';font-size:' + t.tip.size + ';">' + t.tip.txt + '</p>' : ''
		},
		genClose: function (t) {
			return t.close ? '<a class="d-close" href="javascript:void(0);"></a>' : ''
		},
		genButtons: function (t) {
			var e = '';
			if (t.btns && l(t.btns)) {
				e += '<div style="padding:15px 0;">';
				for (var n = 0, i = t.btns.length; i > n; n++) {
					var o = t.btns[n];
					e += o && 'alert' === t.type ? '<a class="' + o.kls + '" id="' + o.id + '">' + o.val + '</a>' : o && 'confirm' === t.type ? '<a class="' + o.kls + '" id="' + o.id + '" style="width: 43%; height: 30px; line-height: 30px; margin:0 3%;">' + o.val + '</a>' : '<a class="' + o.kls + '" id="' + o.id + '" style="margin:5px 0;">' + o.val + '</a>'
				}
				e += '</div>'
			}
			return t.ext && 'string' == typeof t.ext && (e += t.ext),
			e
		},
		addEvents: function (t) {
			var e = null;
			if (t.close) {
				var n = document.getElementsByClassName('d-close') [0];
				n.addEventListener('click', function () {
					s.hide()
				}, !1)
			}
			if (l(t.btns) && t.btns.length) for (var i = 0, o = t.btns.length; o > i; i++) if (e = t.btns[i]) {
				var a = e.event || 'click',
				r = document.getElementById(e.id);
				r && (r.removeEventListener(a, e.handler, !1), r.addEventListener(a, e.handler, !1))
			}
		}
	},
	p = function (t) {
		return this instanceof p ? (new p.fn.init(t), void 0)  : s = new p(t)
	};
	p.fn = p.prototype = {
		constructor: p,
		init: function (e) {
			if (e) {
				var n = document.createElement('div'),
				i = document.createElement('div');
				n.id = 'd-wall',
				i.id = 'd-wrap',
				u.genDom(e, n, i),
				r && document.body.removeChild(r),
				c && document.body.removeChild(c),
				d(n),
				d(i),
				r = n,
				c = i,
				'[object Object]' === Object.prototype.toString.call(e, null) && w.setTimeout(function () {
					u.addEvents(e)
				}, 400)
			}
		},
		show: function () {
			function e(i) {
				w.removeEventListener(i.type, e, !1),
				n.reset.call(n)
			}
			var n = this;
			r && c && (n.reset(), r.style.display = 'block', c.style.display = 'inline-block', w.addEventListener('resize', e, !1), w.addEventListener('scroll', e, !1))
		},
		hide: function () {
			r && c && (r.style.display = 'none', c.style.display = 'none')
		},
		reset: function () {
			if (r && c) {
				c.style.top = (o - c.clientHeight - 20) / 2 + 'px',
				c.style.left = (a - c.clientWidth) / 2 + 'px';
				var t = document.body.scrollHeight || document.documentElement.scrollHeight;
				r.style.width = a + 'px',
				r.style.height = t + 'px'
			}
		}
	},
	n.alert = function (t) {
		var e = {};'string' == typeof arguments[0] && arguments[0] ? (e.title = arguments[1] || '', e.tip = arguments[0], e.btn = {val: arguments[2] || '我知道了'}) : t && 'object' == typeof t && (e = t),
		s = p({
			type: 'alert',
			icon: e.icon || {
				url: img_dir+'/images/i-plaint.png',
				width: '8px',
				height: '36px'
			},
			title: {
				txt: e.title
			},
			tip: {
				txt: e.tip
			},
			btns: [
				{
					id: 'btn-close',
					kls: 'btn-orange',
					event: 'click',
					val: e.btn && e.btn.val || '我知道了',
					handler: function (t) {
						s.hide(),
						e.btn && 'function' == typeof e.btn.handler && e.btn.handler(t)
					}
				}
			]
		}),
		s.show()
	},
	n.confirm = function (t) {
		var e = {};
		'string' == typeof arguments[0] && arguments[0] ? (e.text = arguments[0] || '', e.confirm = {
		}, e.confirm.handler = arguments[1])  : t && 'object' == typeof t && (e = t);
		var n = e.cancel || {},i = e.confirm || {};
		s = p({
			type: 'confirm',
			tip: {
				txt: e.text
			},
			icon: e.icon || {
				url: img_dir + '/images/i-plaint.png'
			},
			btns: [
				{
					id: n.id || 'btn-cancel',
					val: n.val || '取消',
					kls: n.kls || 'btn-white',
					event: n.event || 'click',
					handler: function (t) {
						s.hide(),
						'function' == typeof n.handler && n.handler(t)
					}
				},
				{
					id: i.id || 'btn-ok',
					val: i.val || '确定',
					kls: i.kls || 'btn-orange',
					event: i.event || 'click',
					handler: function (t) {
						s.hide(),
						'function' == typeof i.handler && i.handler(t)
					}
				}
			],
			ext: e.ext
		}),
		s.show()
	},
	n.loading = function (e) {
		var n = {};'object' != typeof arguments[0] ? (n.text = arguments[0], n.time = arguments[1] || 0)  : n = e,
		s = p({
			type: 'loading',
			bg: '#fff',
			d_bg: '#0c0d0d',
			d_op: '0.7',
			width: '140px',
			height: '140px',
			icon: e && e.icon || {
			},
			tip: {
				txt: n.text || '正在加载',
				color: '#fff',
				size: '14px'
			}
		}),s.show(),n.time || (n.time = 5000),w.setTimeout(function () {s.hide(),console.log('function' == typeof n.hideCB),'function' == typeof n.hideCB && n.hideCB()
		}, n.time)
	},
	n.flatLoading = function (e) {
		var n = {};'object' != typeof arguments[0] ? (n.text = arguments[0], n.time = arguments[1] || 0)  : n = e,
		s = p({
			type: 'loading',
			bg: '#fff',
			op: '1',
			d_bg: '#fff',
			d_op: '1',
			width: '140px',
			height: '140px',
			icon: !0,
			tip: {
				txt: n.text || '',
				color: '#666',
				size: '14px'
			}
		}),s.show(),n.time || (n.time = 5000),w.setTimeout(function () {s.hide(),'function' == typeof n.hideCB && n.hideCB()}, n.time)
	},
	n.logoLoading = function (t, e) {
		s = p('<div class="loading-car"><div class="bg"></div><div class="loading-car-icon"></div></div>'),
		s.show(),
		t || (t = 5000),
		setTimeout(function () {s.hide(),'function' == typeof e && e()}, t)
	},
	n.tip = function (e) {
		var n = {};'object' != typeof arguments[0] ? (n.text = arguments[0], n.time = arguments[1] || 0)  : n = e,
		n.time = parseInt(n.time) || 6000,
		s = p({
			type: 'tip',
			bg: '#fff',
			d_bg: '#0c0d0d',
			d_op: '0.7',
			width: '140px',
			height: '140px',
			icon: n.icon || {
				url: img_dir + '/images/i-plaint.png',
				width: '8px',
				height: '36px'
			},
			tip: {
				txt: n.text || '温馨提醒',
				color: '#fff',
				size: '14px'
			}
		}),
		s.show(),
		w.setTimeout(function () {
			s.hide()
		}, n.time)
	},
	n.ok = function (e) {
		var n = {};'object' != typeof arguments[0] ? (n.text = arguments[0], n.time = arguments[1] || 0)  : n = e,
		n.time = parseInt(n.time) || 600,
		s = p({
			type: 'tip',
			bg: '#fff',
			d_bg: '#0c0d0d',
			d_op: '0.7',
			width: '140px',
			height: '140px',
			icon: n.icon || {
				url: img_dir + '/images/i-right.png',
				width: '35px',
				height: '36px'
			},
			tip: {
				txt: n.text || '温馨提醒',
				color: '#fff',
				size: '14px'
			}
		}),
		s.show(),
		w.setTimeout(function () {
			s.hide()
		}, n.time)
	},
	n.close = function(){
		s && s.hide();
		}
	n.Fn = p,
	t.ui = e,
	e.dialog = n
}(G,window);
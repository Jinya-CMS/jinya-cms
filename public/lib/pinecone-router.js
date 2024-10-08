var M = class {
    params = {};
    path;
    handlers = [];

    constructor(o, f = {}) {
      this.path = o, Object.keys(f)
        .forEach(l => {
          this[l] = f[l];
        }), f.template && (this.programmaticTemplate = !0);
    }

    template = '';
    programmaticTemplate = !1;
    handlersDone = !1;
    cancelHandlers;
  },
  C = M;

function I(i) {
  return i.replace(/(^\/+|\/+$)/g, '')
    .split('/');
}

function k(i, o) {
  let f = /(?:\?([^#]*))?(#.*)?$/,
    l = i.match(f),
    w = {},
    _;
  if (l && l[1]) {
    let d = l[1].split('&');
    for (let x = 0; x < d.length; x++) {
      let u = d[x].split('=');
      w[decodeURIComponent(u[0])] = decodeURIComponent(u.slice(1)
        .join('='));
    }
  }
  let P = I(i.replace(f, '')),
    h = I(o || ''),
    E = Math.max(P.length, h.length);
  for (let d = 0; d < E; d++) {
    if (h[d] && h[d].charAt(0) === ':') {
      let x = h[d].replace(/(^:|[+*?]+$)/g, ''),
        u = (h[d].match(/[+*?]+$/) || {}).toString()[0],
        y = ~u.indexOf('+'),
        R = ~u.indexOf('*'),
        b = P[d] || '';
      if (!b && !R && (u.indexOf('?') < 0 || y)) {
        _ = !1;
        break;
      }
      if (w[x] = decodeURIComponent(b), y || R) {
        w[x] = P.slice(d)
          .map(decodeURIComponent)
          .join('/');
        break;
      }
    } else if (h[d] !== P[d]) {
      _ = !1;
      break;
    }
  }
  return _ === !1 ? !1 : w;
}

function T(i, ...o) {
  if (window.PineconeRouterMiddlewares) {
    for (let f in window.PineconeRouterMiddlewares) {
      let l = window.PineconeRouterMiddlewares[f];
      if (l[i] == null) return;
      if (l[i](...o) == 'stop') return 'stop';
    }
  }
}

function D(i) {
  document.dispatchEvent(new CustomEvent('fetch-error', { detail: i }));
}

function S(i) {
  let o = i.reactive({
    version: '4.3.1',
    name: 'pinecone-router',
    settings: {
      hash: !1,
      basePath: '/',
      templateTargetId: null,
    },
    notfound: new C('notfound'),
    routes: [],
    context: {
      route: '',
      path: '',
      params: {},
      query: window.location.search.substring(1),
      hash: window.location.hash.substring(1),
      redirect(e) {
        return g(e), 'stop';
      },
      navigate(e) {
        g(e);
      },
    },
    add(e, n) {
      if (this.routes.find(r => r.path == e) != null) throw new Error('Pinecone Router: route already exist');
      return this.routes.push(new C(e, n)) - 1;
    },
    remove(e) {
      this.routes = this.routes.filter(n => n.path != e);
    },
    loadStart: new Event('pinecone-start'),
    loadEnd: new Event('pinecone-end'),
  });
  window.PineconeRouter = o;
  var f = {},
    l = {};
  let w = new Set;
  var _ = {};
  let P = (e, n, r = !1) => (f[n] && !r ? f[n].then(t => e.innerHTML = t) : f[n] = fetch(n)
      .then(t => t.ok ? t.text() : (D(t.statusText), null))
      .then(t => t == null ? (l[n] = null, f[n] = null, null) : (l[n] = t, e.innerHTML = t, t)), f[n]),
    h = (e, n, r) => {
      if (w.has(n)) return;
      w.add(n);
      let t = e.content.cloneNode(!0).firstElementChild;
      t && (i.addScopeToNode(t, {}, e), i.mutateDom(() => {
        r != null ? r.appendChild(t) : e.after(t), i.initTree(t);
      }), e._x_PineconeRouter_CurrentTemplate = t, e._x_PineconeRouter_undoTemplate = () => {
        t.remove(), delete e._x_PineconeRouter_CurrentTemplate;
      }, i.nextTick(() => w.delete(n)));
    };

  function E(e) {
    e._x_PineconeRouter_undoTemplate && (e._x_PineconeRouter_undoTemplate(), delete e._x_PineconeRouter_undoTemplate);
  }

  function d(e, n, r, t) {
    if (e._x_PineconeRouter_CurrentTemplate) return e._x_PineconeRouter_CurrentTemplate;
    e.content.firstElementChild ? (h(e, n, t), u()) : r && (l[r] ? (e.innerHTML = l[r], h(e, n, t), u()) : _[r] ? _[r].then(() => h(e, n, t)) : P(e, r)
      .then(() => h(e, n, t))
      .finally(() => u()));
  }

  let x = () => {
      document.dispatchEvent(o.loadStart);
    },
    u = () => {
      document.dispatchEvent(o.loadEnd);
    },
    y = e => !o.settings.hash && o.settings.basePath != '/' ? o.settings.basePath + e : e,
    R = e => o.routes.findIndex(n => n.path == e);
  i.directive('route', (e, {
    expression: n,
    modifiers: r,
  }, {
                          effect: t,
                          cleanup: s,
                        }) => {
    let a = n;
    if (T('onBeforeRouteProcessed', e, a), a.indexOf('#') > -1) throw new Error('Pinecone Router: A route\'s path may not have a hash character.');
    let c = L(r, 'target', null) ?? window.PineconeRouter.settings.templateTargetId,
      m = document.getElementById(c);
    if (c && !m) throw new Error('Pinecone Router: Can\'t find an element with the suplied target ID: ' + c);
    let p = null;
    a != 'notfound' && (a = y(a), p = o.add(a));
    let v = o.routes[p] ?? o.notfound;
    e._x_PineconeRouter_route = a, e.content.firstElementChild != null && i.nextTick(() => {
      t(() => {
        v.handlersDone && o.context.route == a ? d(e, n, null, m) : E(e);
      });
    }), s(() => {
      e._x_PineconeRouter_undoTemplate && e._x_PineconeRouter_undoTemplate(), o.remove(a), delete e._x_PineconeRouter_route;
    }), T('onAfterRouteProcessed', e, a);
  }), i.directive('handler', (e, { expression: n }, {
    evaluate: r,
    cleanup: t,
  }) => {
    if (!e._x_PineconeRouter_route) throw new Error('Pinecone Router: x-handler must be set on the same element as x-route.');
    let s;
    !(n.startsWith('[') && n.endsWith(']')) && !(n.startsWith('Array(') && n.endsWith(')')) && (n = `[${n}]`);
    let a = r(n);
    if (typeof a == 'object') s = a; else throw new Error(`Pinecone Router: Invalid handler type: ${typeof a}.`);
    for (let p = 0; p < s.length; p++) s[p] = s[p].bind(i.$data(e));
    let c = e._x_PineconeRouter_route,
      m = c == 'notfound' ? o.notfound : o.routes[R(c)];
    m.handlers = s, t(() => {
      m.handlers = [], m.handlersDone = !0, m.cancelHandlers = !1;
    });
  }), i.directive('template', (e, {
    modifiers: n,
    expression: r,
  }, {
                                 Alpine: t,
                                 effect: s,
                                 cleanup: a,
                               }) => {
    if (!e._x_PineconeRouter_route) throw new Error('Pinecone Router: x-template must be used on the same element as x-route.');
    if (e.content.firstElementChild != null) throw new Error('Pinecone Router: x-template cannot be used alongside an inline template (template element should not have a child).');
    let c = r,
      m = L(n, 'target', null) ?? window.PineconeRouter.settings.templateTargetId,
      p = document.getElementById(m);
    if (m && !p) throw new Error('Pinecone Router: Can\'t find an element with the suplied target ID: ' + m);
    n.includes('preload') && (_[c] = P(e, c)
      .finally(() => {
        _[c] = null, u();
      }));
    let v = e._x_PineconeRouter_route,
      H = v == 'notfound' ? o.notfound : o.routes[R(v)];
    H.template = c, t.nextTick(() => {
      s(() => {
        H.handlersDone && o.context.route == H.path ? d(e, r, c, p) : E(e);
      });
    }), a(() => {
      e._x_PineconeRouter_undoTemplate && e._x_PineconeRouter_undoTemplate();
    });
  }), i.$router = o.context, i.magic('router', () => o.context), document.addEventListener('alpine:initialized', () => {
    T('init'), o.settings.hash == !1 ? g(location.pathname, !1, !0) : g(location.hash.substring(1), !1, !0);
  }), window.addEventListener('popstate', () => {
    o.settings.hash ? window.location.hash != '' && g(window.location.hash.substring(1), !0) : g(window.location.pathname, !0);
  }), b();

  function b() {
    function e(n) {
      if (!n || !n.getAttribute) return;
      let r = n.getAttribute('href'),
        t = n.getAttribute('target');
      if (!(!r || !r.match(/^\//g) || t && !t.match(/^_?self$/i))) return typeof r != 'string' && r.url && (r = r.url), r;
    }

    window.document.body.addEventListener('click', function(n) {
      if (n.ctrlKey || n.metaKey || n.altKey || n.shiftKey || n.button || n.defaultPrevented) return;
      let r = o.routes[R(o.context.route)] ?? o.notfound;
      r.handlersDone || (r.cancelHandlers = !0, u());
      let t = n.target;
      do {
        if (t.localName === 'a' && t.getAttribute('href')) {
          if (t.hasAttribute('data-native') || t.hasAttribute('native')) return;
          let s = e(t);
          s && (g(s), n.stopImmediatePropagation && n.stopImmediatePropagation(), n.stopPropagation && n.stopPropagation(), n.preventDefault());
          break;
        }
      } while (t = t.parentNode);
    });
  }

  async function g(e, n = !1, r = !1) {
    e || (e = '/'), o.context.path = e, o.settings.hash || (o.settings.basePath != '/' && !e.startsWith(o.settings.basePath) && (e = o.settings.basePath + e), e == o.settings.basePath && !e.endsWith('/') && (e += '/'));
    let t = o.routes.find(a => {
      let c = k(e, a.path);
      return a.params = c != !1 ? c : {}, c != !1;
    }) ?? o.notfound;
    t.handlersDone = !t.handlers.length, (t.handlers.length || t.template) && x();
    let s = $(t.path, e, t.params);
    if (o.context = s, T('onBeforeHandlersExecuted', t, e, r) == 'stop') {
      u();
      return;
    }
    if (!n) {
      let a = '';
      if (o.settings.hash ? (a = '#', a += window.location.search + e) : a = e + window.location.search + window.location.hash, !r) history.pushState({ path: a }, '', a); else if (o.settings.hash && e == '/') return o.context = s, g('/', !1, !1);
    }
    if (t && t.handlers.length) {
      if (t.cancelHandlers = !1, !await W(t.handlers, s)) {
        u();
        return;
      }
      t.handlersDone = !0, t.template || u();
    }
    if (t.template && t.programmaticTemplate) {
      let a = document.getElementById(o.settings.templateTargetId);
      l[t.template] ? (a.innerHTML = l[t.template], u()) : P(a, t.template)
        .then(() => {
          a.innerHTML = l[t.template], u();
        });
    }
    T('onHandlersExecuted', t, e, r);
  }

  function $(e, n, r) {
    return {
      route: e,
      path: n,
      params: r,
      query: window.location.search.substring(1),
      hash: window.location.hash.substring(1),
      redirect(t) {
        return g(t), 'stop';
      },
      navigate(t) {
        g(t);
      },
    };
  }

  function L(e, n, r) {
    if (e.indexOf(n) === -1) return r;
    let t = e[e.indexOf(n) + 1];
    if (!t) return r;
    if (n === 'target') {
      let s = t.match(/([a-z0-9_-]+)/);
      if (s) return s[1];
    }
    return t;
  }

  async function W(e, n) {
    for (let r = 0; r < e.length; r++) {
      if (typeof e[r] == 'function') {
        let t = o.routes[R(n.route)] ?? o.notfound;
        if (t.cancelHandlers) return t.cancelHandlers = !1, !1;
        let s;
        if (e[r].constructor.name === 'AsyncFunction' ? s = await e[r](n) : s = e[r](n), s == 'stop') return !1;
      }
    }
    return !0
  }
}

var q = S;
export { q as default };

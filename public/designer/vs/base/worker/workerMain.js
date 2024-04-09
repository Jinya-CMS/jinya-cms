/*!-----------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Version: 0.47.0(69991d66135e4a1fc1cf0b1ac4ad25d429866a0d)
 * Released under the MIT license
 * https://github.com/microsoft/vscode/blob/main/LICENSE.txt
 *-----------------------------------------------------------*/
(function() {
  var J = ['require', 'exports', 'vs/editor/common/core/range', 'vs/editor/common/core/offsetRange', 'vs/editor/common/core/position', 'vs/base/common/errors', 'vs/base/common/strings', 'vs/base/common/arrays', 'vs/editor/common/diff/defaultLinesDiffComputer/algorithms/diffAlgorithm', 'vs/base/common/event', 'vs/editor/common/core/lineRange', 'vs/base/common/arraysFind', 'vs/base/common/assert', 'vs/base/common/lifecycle', 'vs/base/common/objects', 'vs/editor/common/diff/defaultLinesDiffComputer/utils', 'vs/editor/common/diff/rangeMapping', 'vs/base/common/platform', 'vs/base/common/uri', 'vs/nls', 'vs/base/common/functional', 'vs/base/common/iterator', 'vs/base/common/linkedList', 'vs/base/common/stopwatch', 'vs/base/common/diff/diff', 'vs/base/common/types', 'vs/base/common/uint', 'vs/editor/common/core/characterClassifier', 'vs/editor/common/core/wordHelper', 'vs/editor/common/diff/defaultLinesDiffComputer/algorithms/myersDiffAlgorithm', 'vs/editor/common/diff/defaultLinesDiffComputer/linesSliceCharSequence', 'vs/editor/common/diff/linesDiffComputer', 'vs/base/common/cache', 'vs/base/common/color', 'vs/base/common/diff/diffChange', 'vs/base/common/keyCodes', 'vs/base/common/lazy', 'vs/base/common/map', 'vs/base/common/cancellation', 'vs/base/common/hash', 'vs/base/common/codicons', 'vs/editor/common/core/selection', 'vs/editor/common/core/wordCharacterClassifier', 'vs/editor/common/diff/defaultLinesDiffComputer/heuristicSequenceOptimizations', 'vs/editor/common/diff/defaultLinesDiffComputer/lineSequence', 'vs/editor/common/diff/defaultLinesDiffComputer/algorithms/dynamicProgrammingDiffing', 'vs/editor/common/diff/defaultLinesDiffComputer/computeMovedLines', 'vs/editor/common/diff/defaultLinesDiffComputer/defaultLinesDiffComputer', 'vs/editor/common/diff/legacyLinesDiffComputer', 'vs/editor/common/diff/linesDiffComputers', 'vs/editor/common/languages/defaultDocumentColorsComputer', 'vs/editor/common/languages/linkComputer', 'vs/editor/common/languages/supports/inplaceReplaceSupport', 'vs/editor/common/model', 'vs/editor/common/model/prefixSumComputer', 'vs/editor/common/model/mirrorTextModel', 'vs/editor/common/model/textModelSearch', 'vs/editor/common/services/unicodeTextModelHighlighter', 'vs/editor/common/standalone/standaloneEnums', 'vs/editor/common/tokenizationRegistry', 'vs/nls!vs/base/common/platform', 'vs/nls!vs/base/common/worker/simpleWorker', 'vs/base/common/process', 'vs/base/common/path', 'vs/nls!vs/editor/common/languages', 'vs/editor/common/languages', 'vs/editor/common/services/editorBaseApi', 'vs/base/common/worker/simpleWorker', 'vs/editor/common/services/editorSimpleWorker'],
    Z = function(q) {
      for (var t = [], M = 0, R = q.length; M < R; M++) t[M] = J[q[M]];
      return t;
    };
  const Ae = this,
    Re = typeof global == 'object' ? global : {};
  var ce;
  (function(q) {
    q.global = Ae;

    class t {
      constructor() {
        this._detected = !1, this._isWindows = !1, this._isNode = !1, this._isElectronRenderer = !1, this._isWebWorker = !1, this._isElectronNodeIntegrationWebWorker = !1;
      }

      get isWindows() {
        return this._detect(), this._isWindows;
      }

      get isNode() {
        return this._detect(), this._isNode;
      }

      get isElectronRenderer() {
        return this._detect(), this._isElectronRenderer;
      }

      get isWebWorker() {
        return this._detect(), this._isWebWorker;
      }

      get isElectronNodeIntegrationWebWorker() {
        return this._detect(), this._isElectronNodeIntegrationWebWorker;
      }

      static _isWindows() {
        return typeof navigator < 'u' && navigator.userAgent && navigator.userAgent.indexOf('Windows') >= 0 ? !0 : typeof process < 'u' ? process.platform === 'win32' : !1;
      }

      _detect() {
        this._detected || (this._detected = !0, this._isWindows = t._isWindows(), this._isNode = typeof module < 'u' && !!module.exports, this._isElectronRenderer = typeof process < 'u' && typeof process.versions < 'u' && typeof process.versions.electron < 'u' && process.type === 'renderer', this._isWebWorker = typeof q.global.importScripts == 'function', this._isElectronNodeIntegrationWebWorker = this._isWebWorker && typeof process < 'u' && typeof process.versions < 'u' && typeof process.versions.electron < 'u' && process.type === 'worker');
      }
    }

    q.Environment = t;
  })(ce || (ce = {}));
  var ce;
  (function(q) {
    class t {
      constructor(d, _, p) {
        this.type = d, this.detail = _, this.timestamp = p;
      }
    }

    q.LoaderEvent = t;

    class M {
      constructor(d) {
        this._events = [new t(1, '', d)];
      }

      record(d, _) {
        this._events.push(new t(d, _, q.Utilities.getHighPerformanceTimestamp()));
      }

      getEvents() {
        return this._events;
      }
    }

    q.LoaderEventRecorder = M;

    class R {
      record(d, _) {
      }

      getEvents() {
        return [];
      }
    }

    R.INSTANCE = new R, q.NullLoaderEventRecorder = R;
  })(ce || (ce = {}));
  var ce;
  (function(q) {
    class t {
      static fileUriToFilePath(R, i) {
        if (i = decodeURI(i)
          .replace(/%23/g, '#'), R) {
          if (/^file:\/\/\//.test(i)) return i.substr(8);
          if (/^file:\/\//.test(i)) return i.substr(5);
        } else if (/^file:\/\//.test(i)) return i.substr(7);
        return i;
      }

      static startsWith(R, i) {
        return R.length >= i.length && R.substr(0, i.length) === i;
      }

      static endsWith(R, i) {
        return R.length >= i.length && R.substr(R.length - i.length) === i;
      }

      static containsQueryString(R) {
        return /^[^\#]*\?/gi.test(R);
      }

      static isAbsolutePath(R) {
        return /^((http:\/\/)|(https:\/\/)|(file:\/\/)|(\/))/.test(R);
      }

      static forEachProperty(R, i) {
        if (R) {
          let d;
          for (d in R) R.hasOwnProperty(d) && i(d, R[d]);
        }
      }

      static isEmpty(R) {
        let i = !0;
        return t.forEachProperty(R, () => {
          i = !1;
        }), i;
      }

      static recursiveClone(R) {
        if (!R || typeof R != 'object' || R instanceof RegExp || !Array.isArray(R) && Object.getPrototypeOf(R) !== Object.prototype) return R;
        let i = Array.isArray(R) ? [] : {};
        return t.forEachProperty(R, (d, _) => {
          _ && typeof _ == 'object' ? i[d] = t.recursiveClone(_) : i[d] = _;
        }), i;
      }

      static generateAnonymousModule() {
        return '===anonymous' + t.NEXT_ANONYMOUS_ID++ + '===';
      }

      static isAnonymousModule(R) {
        return t.startsWith(R, '===anonymous');
      }

      static getHighPerformanceTimestamp() {
        return this.PERFORMANCE_NOW_PROBED || (this.PERFORMANCE_NOW_PROBED = !0, this.HAS_PERFORMANCE_NOW = q.global.performance && typeof q.global.performance.now == 'function'), this.HAS_PERFORMANCE_NOW ? q.global.performance.now() : Date.now();
      }
    }

    t.NEXT_ANONYMOUS_ID = 1, t.PERFORMANCE_NOW_PROBED = !1, t.HAS_PERFORMANCE_NOW = !1, q.Utilities = t;
  })(ce || (ce = {}));
  var ce;
  (function(q) {
    function t(i) {
      if (i instanceof Error) return i;
      const d = new Error(i.message || String(i) || 'Unknown Error');
      return i.stack && (d.stack = i.stack), d;
    }

    q.ensureError = t;

    class M {
      static validateConfigurationOptions(d) {
        function _(p) {
          if (p.phase === 'loading') {
            console.error('Loading "' + p.moduleId + '" failed'), console.error(p), console.error('Here are the modules that depend on it:'), console.error(p.neededBy);
            return;
          }
          if (p.phase === 'factory') {
            console.error('The factory function of "' + p.moduleId + '" has thrown an exception'), console.error(p), console.error('Here are the modules that depend on it:'), console.error(p.neededBy);
            return;
          }
        }

        if (d = d || {}, typeof d.baseUrl != 'string' && (d.baseUrl = ''), typeof d.isBuild != 'boolean' && (d.isBuild = !1), typeof d.paths != 'object' && (d.paths = {}), typeof d.config != 'object' && (d.config = {}), typeof d.catchError > 'u' && (d.catchError = !1), typeof d.recordStats > 'u' && (d.recordStats = !1), typeof d.urlArgs != 'string' && (d.urlArgs = ''), typeof d.onError != 'function' && (d.onError = _), Array.isArray(d.ignoreDuplicateModules) || (d.ignoreDuplicateModules = []), d.baseUrl.length > 0 && (q.Utilities.endsWith(d.baseUrl, '/') || (d.baseUrl += '/')), typeof d.cspNonce != 'string' && (d.cspNonce = ''), typeof d.preferScriptTags > 'u' && (d.preferScriptTags = !1), d.nodeCachedData && typeof d.nodeCachedData == 'object' && (typeof d.nodeCachedData.seed != 'string' && (d.nodeCachedData.seed = 'seed'), (typeof d.nodeCachedData.writeDelay != 'number' || d.nodeCachedData.writeDelay < 0) && (d.nodeCachedData.writeDelay = 1e3 * 7), !d.nodeCachedData.path || typeof d.nodeCachedData.path != 'string')) {
          const p = t(new Error('INVALID cached data configuration, \'path\' MUST be set'));
          p.phase = 'configuration', d.onError(p), d.nodeCachedData = void 0;
        }
        return d;
      }

      static mergeConfigurationOptions(d = null, _ = null) {
        let p = q.Utilities.recursiveClone(_ || {});
        return q.Utilities.forEachProperty(d, (c, o) => {
          c === 'ignoreDuplicateModules' && typeof p.ignoreDuplicateModules < 'u' ? p.ignoreDuplicateModules = p.ignoreDuplicateModules.concat(o) : c === 'paths' && typeof p.paths < 'u' ? q.Utilities.forEachProperty(o, (L, e) => p.paths[L] = e) : c === 'config' && typeof p.config < 'u' ? q.Utilities.forEachProperty(o, (L, e) => p.config[L] = e) : p[c] = q.Utilities.recursiveClone(o);
        }), M.validateConfigurationOptions(p);
      }
    }

    q.ConfigurationOptionsUtil = M;

    class R {
      constructor(d, _) {
        if (this._env = d, this.options = M.mergeConfigurationOptions(_), this._createIgnoreDuplicateModulesMap(), this._createSortedPathsRules(), this.options.baseUrl === '' && this.options.nodeRequire && this.options.nodeRequire.main && this.options.nodeRequire.main.filename && this._env.isNode) {
          let p = this.options.nodeRequire.main.filename,
            c = Math.max(p.lastIndexOf('/'), p.lastIndexOf('\\'));
          this.options.baseUrl = p.substring(0, c + 1);
        }
      }

      _createIgnoreDuplicateModulesMap() {
        this.ignoreDuplicateModulesMap = {};
        for (let d = 0; d < this.options.ignoreDuplicateModules.length; d++) this.ignoreDuplicateModulesMap[this.options.ignoreDuplicateModules[d]] = !0;
      }

      _createSortedPathsRules() {
        this.sortedPathsRules = [], q.Utilities.forEachProperty(this.options.paths, (d, _) => {
          Array.isArray(_) ? this.sortedPathsRules.push({
            from: d,
            to: _,
          }) : this.sortedPathsRules.push({
            from: d,
            to: [_],
          });
        }), this.sortedPathsRules.sort((d, _) => _.from.length - d.from.length);
      }

      cloneAndMerge(d) {
        return new R(this._env, M.mergeConfigurationOptions(d, this.options));
      }

      getOptionsLiteral() {
        return this.options;
      }

      _applyPaths(d) {
        let _;
        for (let p = 0, c = this.sortedPathsRules.length; p < c; p++) {
          if (_ = this.sortedPathsRules[p], q.Utilities.startsWith(d, _.from)) {
            let o = [];
            for (let L = 0, e = _.to.length; L < e; L++) o.push(_.to[L] + d.substr(_.from.length));
            return o;
          }
        }
        return [d];
      }

      _addUrlArgsToUrl(d) {
        return q.Utilities.containsQueryString(d) ? d + '&' + this.options.urlArgs : d + '?' + this.options.urlArgs;
      }

      _addUrlArgsIfNecessaryToUrl(d) {
        return this.options.urlArgs ? this._addUrlArgsToUrl(d) : d;
      }

      _addUrlArgsIfNecessaryToUrls(d) {
        if (this.options.urlArgs) for (let _ = 0, p = d.length; _ < p; _++) d[_] = this._addUrlArgsToUrl(d[_]);
        return d;
      }

      moduleIdToPaths(d) {
        if (this._env.isNode && this.options.amdModulesPattern instanceof RegExp && !this.options.amdModulesPattern.test(d)) return this.isBuild() ? ['empty:'] : ['node|' + d];
        let _ = d,
          p;
        if (!q.Utilities.endsWith(_, '.js') && !q.Utilities.isAbsolutePath(_)) {
          p = this._applyPaths(_);
          for (let c = 0, o = p.length; c < o; c++) this.isBuild() && p[c] === 'empty:' || (q.Utilities.isAbsolutePath(p[c]) || (p[c] = this.options.baseUrl + p[c]), !q.Utilities.endsWith(p[c], '.js') && !q.Utilities.containsQueryString(p[c]) && (p[c] = p[c] + '.js'));
        } else {
          !q.Utilities.endsWith(_, '.js') && !q.Utilities.containsQueryString(_) && (_ = _ + '.js'), p = [_];
        }
        return this._addUrlArgsIfNecessaryToUrls(p);
      }

      requireToUrl(d) {
        let _ = d;
        return q.Utilities.isAbsolutePath(_) || (_ = this._applyPaths(_)[0], q.Utilities.isAbsolutePath(_) || (_ = this.options.baseUrl + _)), this._addUrlArgsIfNecessaryToUrl(_);
      }

      isBuild() {
        return this.options.isBuild;
      }

      shouldInvokeFactory(d) {
        return !!(!this.options.isBuild || q.Utilities.isAnonymousModule(d) || this.options.buildForceInvokeFactory && this.options.buildForceInvokeFactory[d]);
      }

      isDuplicateMessageIgnoredFor(d) {
        return this.ignoreDuplicateModulesMap.hasOwnProperty(d);
      }

      getConfigForModule(d) {
        if (this.options.config) return this.options.config[d];
      }

      shouldCatchError() {
        return this.options.catchError;
      }

      shouldRecordStats() {
        return this.options.recordStats;
      }

      onError(d) {
        this.options.onError(d);
      }
    }

    q.Configuration = R;
  })(ce || (ce = {}));
  var ce;
  (function(q) {
    class t {
      constructor(o) {
        this._env = o, this._scriptLoader = null, this._callbackMap = {};
      }

      load(o, L, e, s) {
        if (!this._scriptLoader) {
          if (this._env.isWebWorker) {
            this._scriptLoader = new i;
          } else if (this._env.isElectronRenderer) {
            const { preferScriptTags: u } = o.getConfig()
              .getOptionsLiteral();
            u ? this._scriptLoader = new M : this._scriptLoader = new d(this._env);
          } else {
            this._env.isNode ? this._scriptLoader = new d(this._env) : this._scriptLoader = new M;
          }
        }
        let l = {
          callback: e,
          errorback: s,
        };
        if (this._callbackMap.hasOwnProperty(L)) {
          this._callbackMap[L].push(l);
          return;
        }
        this._callbackMap[L] = [l], this._scriptLoader.load(o, L, () => this.triggerCallback(L), u => this.triggerErrorback(L, u));
      }

      triggerCallback(o) {
        let L = this._callbackMap[o];
        delete this._callbackMap[o];
        for (let e = 0; e < L.length; e++) L[e].callback();
      }

      triggerErrorback(o, L) {
        let e = this._callbackMap[o];
        delete this._callbackMap[o];
        for (let s = 0; s < e.length; s++) e[s].errorback(L);
      }
    }

    class M {
      attachListeners(o, L, e) {
        let s = () => {
            o.removeEventListener('load', l), o.removeEventListener('error', u);
          },
          l = b => {
            s(), L();
          },
          u = b => {
            s(), e(b);
          };
        o.addEventListener('load', l), o.addEventListener('error', u);
      }

      load(o, L, e, s) {
        if (/^node\|/.test(L)) {
          let l = o.getConfig()
              .getOptionsLiteral(),
            u = _(o.getRecorder(), l.nodeRequire || q.global.nodeRequire),
            b = L.split('|'),
            f = null;
          try {
            f = u(b[1]);
          } catch (y) {
            s(y);
            return;
          }
          o.enqueueDefineAnonymousModule([], () => f), e();
        } else {
          let l = document.createElement('script');
          l.setAttribute('async', 'async'), l.setAttribute('type', 'text/javascript'), this.attachListeners(l, e, s);
          const { trustedTypesPolicy: u } = o.getConfig()
            .getOptionsLiteral();
          u && (L = u.createScriptURL(L)), l.setAttribute('src', L);
          const { cspNonce: b } = o.getConfig()
            .getOptionsLiteral();
          b && l.setAttribute('nonce', b), document.getElementsByTagName('head')[0].appendChild(l);
        }
      }
    }

    function R(c) {
      const { trustedTypesPolicy: o } = c.getConfig()
        .getOptionsLiteral();
      try {
        return (o ? self.eval(o.createScript('', 'true')) : new Function('true')).call(self), !0;
      } catch {
        return !1;
      }
    }

    class i {
      constructor() {
        this._cachedCanUseEval = null;
      }

      _canUseEval(o) {
        return this._cachedCanUseEval === null && (this._cachedCanUseEval = R(o)), this._cachedCanUseEval;
      }

      load(o, L, e, s) {
        if (/^node\|/.test(L)) {
          const l = o.getConfig()
              .getOptionsLiteral(),
            u = _(o.getRecorder(), l.nodeRequire || q.global.nodeRequire),
            b = L.split('|');
          let f = null;
          try {
            f = u(b[1]);
          } catch (y) {
            s(y);
            return;
          }
          o.enqueueDefineAnonymousModule([], function() {
            return f;
          }), e();
        } else {
          const { trustedTypesPolicy: l } = o.getConfig()
            .getOptionsLiteral();
          if (!(/^((http:)|(https:)|(file:))/.test(L) && L.substring(0, self.origin.length) !== self.origin) && this._canUseEval(o)) {
            fetch(L)
              .then(b => {
                if (b.status !== 200) throw new Error(b.statusText);
                return b.text();
              })
              .then(b => {
                b = `${b}
//# sourceURL=${L}`, (l ? self.eval(l.createScript('', b)) : new Function(b)).call(self), e();
              })
              .then(void 0, s);
            return;
          }
          try {
            l && (L = l.createScriptURL(L)), importScripts(L), e();
          } catch (b) {
            s(b);
          }
        }
      }
    }

    class d {
      constructor(o) {
        this._env = o, this._didInitialize = !1, this._didPatchNodeRequire = !1;
      }

      _init(o) {
        this._didInitialize || (this._didInitialize = !0, this._fs = o('fs'), this._vm = o('vm'), this._path = o('path'), this._crypto = o('crypto'));
      }

      _initNodeRequire(o, L) {
        const { nodeCachedData: e } = L.getConfig()
          .getOptionsLiteral();
        if (!e || this._didPatchNodeRequire) return;
        this._didPatchNodeRequire = !0;
        const s = this,
          l = o('module');

        function u(b) {
          const f = b.constructor;
          let y = function(E) {
            try {
              return b.require(E);
            } finally {
            }
          };
          return y.resolve = function(E, S) {
            return f._resolveFilename(E, b, !1, S);
          }, y.resolve.paths = function(E) {
            return f._resolveLookupPaths(E, b);
          }, y.main = process.mainModule, y.extensions = f._extensions, y.cache = f._cache, y;
        }

        l.prototype._compile = function(b, f) {
          const y = l.wrap(b.replace(/^#!.*/, '')),
            w = L.getRecorder(),
            E = s._getCachedDataPath(e, f),
            S = { filename: f };
          let C;
          try {
            const N = s._fs.readFileSync(E);
            C = N.slice(0, 16), S.cachedData = N.slice(16), w.record(60, E);
          } catch {
            w.record(61, E);
          }
          const r = new s._vm.Script(y, S),
            a = r.runInThisContext(S),
            g = s._path.dirname(f),
            m = u(this),
            h = [this.exports, m, this, f, g, process, Re, Buffer],
            v = a.apply(this.exports, h);
          return s._handleCachedData(r, y, E, !S.cachedData, L), s._verifyCachedData(r, y, E, C, L), v;
        };
      }

      load(o, L, e, s) {
        const l = o.getConfig()
            .getOptionsLiteral(),
          u = _(o.getRecorder(), l.nodeRequire || q.global.nodeRequire),
          b = l.nodeInstrumenter || function(y) {
            return y;
          };
        this._init(u), this._initNodeRequire(u, o);
        let f = o.getRecorder();
        if (/^node\|/.test(L)) {
          let y = L.split('|'),
            w = null;
          try {
            w = u(y[1]);
          } catch (E) {
            s(E);
            return;
          }
          o.enqueueDefineAnonymousModule([], () => w), e();
        } else {
          L = q.Utilities.fileUriToFilePath(this._env.isWindows, L);
          const y = this._path.normalize(L),
            w = this._getElectronRendererScriptPathOrUri(y),
            E = !!l.nodeCachedData,
            S = E ? this._getCachedDataPath(l.nodeCachedData, L) : void 0;
          this._readSourceAndCachedData(y, S, f, (C, r, a, g) => {
            if (C) {
              s(C);
              return;
            }
            let m;
            r.charCodeAt(0) === d._BOM ? m = d._PREFIX + r.substring(1) + d._SUFFIX : m = d._PREFIX + r + d._SUFFIX, m = b(m, y);
            const h = {
                filename: w,
                cachedData: a,
              },
              v = this._createAndEvalScript(o, m, h, e, s);
            this._handleCachedData(v, m, S, E && !a, o), this._verifyCachedData(v, m, S, g, o);
          });
        }
      }

      _createAndEvalScript(o, L, e, s, l) {
        const u = o.getRecorder();
        u.record(31, e.filename);
        const b = new this._vm.Script(L, e),
          f = b.runInThisContext(e),
          y = o.getGlobalAMDDefineFunc();
        let w = !1;
        const E = function() {
          return w = !0, y.apply(null, arguments);
        };
        return E.amd = y.amd, f.call(q.global, o.getGlobalAMDRequireFunc(), E, e.filename, this._path.dirname(e.filename)), u.record(32, e.filename), w ? s() : l(new Error(`Didn't receive define call in ${e.filename}!`)), b;
      }

      _getElectronRendererScriptPathOrUri(o) {
        if (!this._env.isElectronRenderer) return o;
        let L = o.match(/^([a-z])\:(.*)/i);
        return L ? `file:///${(L[1].toUpperCase() + ':' + L[2]).replace(/\\/g, '/')}` : `file://${o}`;
      }

      _getCachedDataPath(o, L) {
        const e = this._crypto.createHash('md5')
            .update(L, 'utf8')
            .update(o.seed, 'utf8')
            .update(process.arch, '')
            .digest('hex'),
          s = this._path.basename(L)
            .replace(/\.js$/, '');
        return this._path.join(o.path, `${s}-${e}.code`);
      }

      _handleCachedData(o, L, e, s, l) {
        o.cachedDataRejected ? this._fs.unlink(e, u => {
          l.getRecorder()
            .record(62, e), this._createAndWriteCachedData(o, L, e, l), u && l.getConfig()
            .onError(u);
        }) : s && this._createAndWriteCachedData(o, L, e, l);
      }

      _createAndWriteCachedData(o, L, e, s) {
        let l = Math.ceil(s.getConfig()
            .getOptionsLiteral().nodeCachedData.writeDelay * (1 + Math.random())),
          u = -1,
          b = 0,
          f;
        const y = () => {
          setTimeout(() => {
            f || (f = this._crypto.createHash('md5')
              .update(L, 'utf8')
              .digest());
            const w = o.createCachedData();
            if (!(w.length === 0 || w.length === u || b >= 5)) {
              if (w.length < u) {
                y();
                return;
              }
              u = w.length, this._fs.writeFile(e, Buffer.concat([f, w]), E => {
                E && s.getConfig()
                  .onError(E), s.getRecorder()
                  .record(63, e), y();
              });
            }
          }, l * Math.pow(4, b++));
        };
        y();
      }

      _readSourceAndCachedData(o, L, e, s) {
        if (!L) {
          this._fs.readFile(o, { encoding: 'utf8' }, s);
        } else {
          let l,
            u,
            b,
            f = 2;
          const y = w => {
            w ? s(w) : --f === 0 && s(void 0, l, u, b);
          };
          this._fs.readFile(o, { encoding: 'utf8' }, (w, E) => {
            l = E, y(w);
          }), this._fs.readFile(L, (w, E) => {
            !w && E && E.length > 0 ? (b = E.slice(0, 16), u = E.slice(16), e.record(60, L)) : e.record(61, L), y();
          });
        }
      }

      _verifyCachedData(o, L, e, s, l) {
        s && (o.cachedDataRejected || setTimeout(() => {
          const u = this._crypto.createHash('md5')
            .update(L, 'utf8')
            .digest();
          s.equals(u) || (l.getConfig()
            .onError(new Error(`FAILED TO VERIFY CACHED DATA, deleting stale '${e}' now, but a RESTART IS REQUIRED`)), this._fs.unlink(e, b => {
            b && l.getConfig()
              .onError(b);
          }));
        }, Math.ceil(5e3 * (1 + Math.random()))));
      }
    }

    d._BOM = 65279, d._PREFIX = '(function (require, define, __filename, __dirname) { ', d._SUFFIX = `
});`;

    function _(c, o) {
      if (o.__$__isRecorded) return o;
      const L = function(s) {
        c.record(33, s);
        try {
          return o(s);
        } finally {
          c.record(34, s);
        }
      };
      return L.__$__isRecorded = !0, L;
    }

    q.ensureRecordedNodeRequire = _;

    function p(c) {
      return new t(c);
    }

    q.createScriptLoader = p;
  })(ce || (ce = {}));
  var ce;
  (function(q) {
    class t {
      constructor(c) {
        let o = c.lastIndexOf('/');
        o !== -1 ? this.fromModulePath = c.substr(0, o + 1) : this.fromModulePath = '';
      }

      static _normalizeModuleId(c) {
        let o = c,
          L;
        for (L = /\/\.\//; L.test(o);) o = o.replace(L, '/');
        for (o = o.replace(/^\.\//g, ''), L = /\/(([^\/])|([^\/][^\/\.])|([^\/\.][^\/])|([^\/][^\/][^\/]+))\/\.\.\//; L.test(o);) o = o.replace(L, '/');
        return o = o.replace(/^(([^\/])|([^\/][^\/\.])|([^\/\.][^\/])|([^\/][^\/][^\/]+))\/\.\.\//, ''), o;
      }

      resolveModule(c) {
        let o = c;
        return q.Utilities.isAbsolutePath(o) || (q.Utilities.startsWith(o, './') || q.Utilities.startsWith(o, '../')) && (o = t._normalizeModuleId(this.fromModulePath + o)), o;
      }
    }

    t.ROOT = new t(''), q.ModuleIdResolver = t;

    class M {
      constructor(c, o, L, e, s, l) {
        this.id = c, this.strId = o, this.dependencies = L, this._callback = e, this._errorback = s, this.moduleIdResolver = l, this.exports = {}, this.error = null, this.exportsPassedIn = !1, this.unresolvedDependenciesCount = this.dependencies.length, this._isComplete = !1;
      }

      static _safeInvokeFunction(c, o) {
        try {
          return {
            returnedValue: c.apply(q.global, o),
            producedError: null,
          };
        } catch (L) {
          return {
            returnedValue: null,
            producedError: L,
          };
        }
      }

      static _invokeFactory(c, o, L, e) {
        return c.shouldInvokeFactory(o) ? c.shouldCatchError() ? this._safeInvokeFunction(L, e) : {
          returnedValue: L.apply(q.global, e),
          producedError: null,
        } : {
          returnedValue: null,
          producedError: null,
        };
      }

      complete(c, o, L, e) {
        this._isComplete = !0;
        let s = null;
        if (this._callback) {
          if (typeof this._callback == 'function') {
            c.record(21, this.strId);
            let l = M._invokeFactory(o, this.strId, this._callback, L);
            s = l.producedError, c.record(22, this.strId), !s && typeof l.returnedValue < 'u' && (!this.exportsPassedIn || q.Utilities.isEmpty(this.exports)) && (this.exports = l.returnedValue);
          } else {
            this.exports = this._callback;
          }
        }
        if (s) {
          let l = q.ensureError(s);
          l.phase = 'factory', l.moduleId = this.strId, l.neededBy = e(this.id), this.error = l, o.onError(l);
        }
        this.dependencies = null, this._callback = null, this._errorback = null, this.moduleIdResolver = null;
      }

      onDependencyError(c) {
        return this._isComplete = !0, this.error = c, this._errorback ? (this._errorback(c), !0) : !1;
      }

      isComplete() {
        return this._isComplete;
      }
    }

    q.Module = M;

    class R {
      constructor() {
        this._nextId = 0, this._strModuleIdToIntModuleId = new Map, this._intModuleIdToStrModuleId = [], this.getModuleId('exports'), this.getModuleId('module'), this.getModuleId('require');
      }

      getMaxModuleId() {
        return this._nextId;
      }

      getModuleId(c) {
        let o = this._strModuleIdToIntModuleId.get(c);
        return typeof o > 'u' && (o = this._nextId++, this._strModuleIdToIntModuleId.set(c, o), this._intModuleIdToStrModuleId[o] = c), o;
      }

      getStrModuleId(c) {
        return this._intModuleIdToStrModuleId[c];
      }
    }

    class i {
      constructor(c) {
        this.id = c;
      }
    }

    i.EXPORTS = new i(0), i.MODULE = new i(1), i.REQUIRE = new i(2), q.RegularDependency = i;

    class d {
      constructor(c, o, L) {
        this.id = c, this.pluginId = o, this.pluginParam = L;
      }
    }

    q.PluginDependency = d;

    class _ {
      constructor(c, o, L, e, s = 0) {
        this._env = c, this._scriptLoader = o, this._loaderAvailableTimestamp = s, this._defineFunc = L, this._requireFunc = e, this._moduleIdProvider = new R, this._config = new q.Configuration(this._env), this._hasDependencyCycle = !1, this._modules2 = [], this._knownModules2 = [], this._inverseDependencies2 = [], this._inversePluginDependencies2 = new Map, this._currentAnonymousDefineCall = null, this._recorder = null, this._buildInfoPath = [], this._buildInfoDefineStack = [], this._buildInfoDependencies = [], this._requireFunc.moduleManager = this;
      }

      static _findRelevantLocationInStack(c, o) {
        let L = l => l.replace(/\\/g, '/'),
          e = L(c),
          s = o.split(/\n/);
        for (let l = 0; l < s.length; l++) {
          let u = s[l].match(/(.*):(\d+):(\d+)\)?$/);
          if (u) {
            let b = u[1],
              f = u[2],
              y = u[3],
              w = Math.max(b.lastIndexOf(' ') + 1, b.lastIndexOf('(') + 1);
            if (b = b.substr(w), b = L(b), b === e) {
              let E = {
                line: parseInt(f, 10),
                col: parseInt(y, 10),
              };
              return E.line === 1 && (E.col -= 53), E;
            }
          }
        }
        throw new Error('Could not correlate define call site for needle ' + c);
      }

      reset() {
        return new _(this._env, this._scriptLoader, this._defineFunc, this._requireFunc, this._loaderAvailableTimestamp);
      }

      getGlobalAMDDefineFunc() {
        return this._defineFunc;
      }

      getGlobalAMDRequireFunc() {
        return this._requireFunc;
      }

      getBuildInfo() {
        if (!this._config.isBuild()) return null;
        let c = [],
          o = 0;
        for (let L = 0, e = this._modules2.length; L < e; L++) {
          let s = this._modules2[L];
          if (!s) continue;
          let l = this._buildInfoPath[s.id] || null,
            u = this._buildInfoDefineStack[s.id] || null,
            b = this._buildInfoDependencies[s.id];
          c[o++] = {
            id: s.strId,
            path: l,
            defineLocation: l && u ? _._findRelevantLocationInStack(l, u) : null,
            dependencies: b,
            shim: null,
            exports: s.exports,
          };
        }
        return c;
      }

      getRecorder() {
        return this._recorder || (this._config.shouldRecordStats() ? this._recorder = new q.LoaderEventRecorder(this._loaderAvailableTimestamp) : this._recorder = q.NullLoaderEventRecorder.INSTANCE), this._recorder;
      }

      getLoaderEvents() {
        return this.getRecorder()
          .getEvents();
      }

      enqueueDefineAnonymousModule(c, o) {
        if (this._currentAnonymousDefineCall !== null) throw new Error('Can only have one anonymous define call per script file');
        let L = null;
        this._config.isBuild() && (L = new Error('StackLocation').stack || null), this._currentAnonymousDefineCall = {
          stack: L,
          dependencies: c,
          callback: o,
        };
      }

      defineModule(c, o, L, e, s, l = new t(c)) {
        let u = this._moduleIdProvider.getModuleId(c);
        if (this._modules2[u]) {
          this._config.isDuplicateMessageIgnoredFor(c) || console.warn('Duplicate definition of module \'' + c + '\'');
          return;
        }
        let b = new M(u, c, this._normalizeDependencies(o, l), L, e, l);
        this._modules2[u] = b, this._config.isBuild() && (this._buildInfoDefineStack[u] = s, this._buildInfoDependencies[u] = (b.dependencies || []).map(f => this._moduleIdProvider.getStrModuleId(f.id))), this._resolve(b);
      }

      _normalizeDependency(c, o) {
        if (c === 'exports') return i.EXPORTS;
        if (c === 'module') return i.MODULE;
        if (c === 'require') return i.REQUIRE;
        let L = c.indexOf('!');
        if (L >= 0) {
          let e = o.resolveModule(c.substr(0, L)),
            s = o.resolveModule(c.substr(L + 1)),
            l = this._moduleIdProvider.getModuleId(e + '!' + s),
            u = this._moduleIdProvider.getModuleId(e);
          return new d(l, u, s);
        }
        return new i(this._moduleIdProvider.getModuleId(o.resolveModule(c)));
      }

      _normalizeDependencies(c, o) {
        let L = [],
          e = 0;
        for (let s = 0, l = c.length; s < l; s++) L[e++] = this._normalizeDependency(c[s], o);
        return L;
      }

      _relativeRequire(c, o, L, e) {
        if (typeof o == 'string') return this.synchronousRequire(o, c);
        this.defineModule(q.Utilities.generateAnonymousModule(), o, L, e, null, c);
      }

      synchronousRequire(c, o = new t(c)) {
        let L = this._normalizeDependency(c, o),
          e = this._modules2[L.id];
        if (!e) throw new Error('Check dependency list! Synchronous require cannot resolve module \'' + c + '\'. This is the first mention of this module!');
        if (!e.isComplete()) throw new Error('Check dependency list! Synchronous require cannot resolve module \'' + c + '\'. This module has not been resolved completely yet.');
        if (e.error) throw e.error;
        return e.exports;
      }

      configure(c, o) {
        let L = this._config.shouldRecordStats();
        o ? this._config = new q.Configuration(this._env, c) : this._config = this._config.cloneAndMerge(c), this._config.shouldRecordStats() && !L && (this._recorder = null);
      }

      getConfig() {
        return this._config;
      }

      _onLoad(c) {
        if (this._currentAnonymousDefineCall !== null) {
          let o = this._currentAnonymousDefineCall;
          this._currentAnonymousDefineCall = null, this.defineModule(this._moduleIdProvider.getStrModuleId(c), o.dependencies, o.callback, null, o.stack);
        }
      }

      _createLoadError(c, o) {
        let L = this._moduleIdProvider.getStrModuleId(c),
          e = (this._inverseDependencies2[c] || []).map(l => this._moduleIdProvider.getStrModuleId(l));
        const s = q.ensureError(o);
        return s.phase = 'loading', s.moduleId = L, s.neededBy = e, s;
      }

      _onLoadError(c, o) {
        const L = this._createLoadError(c, o);
        this._modules2[c] || (this._modules2[c] = new M(c, this._moduleIdProvider.getStrModuleId(c), [], () => {
        }, null, null));
        let e = [];
        for (let u = 0, b = this._moduleIdProvider.getMaxModuleId(); u < b; u++) e[u] = !1;
        let s = !1,
          l = [];
        for (l.push(c), e[c] = !0; l.length > 0;) {
          let u = l.shift(),
            b = this._modules2[u];
          b && (s = b.onDependencyError(L) || s);
          let f = this._inverseDependencies2[u];
          if (f) {
            for (let y = 0, w = f.length; y < w; y++) {
              let E = f[y];
              e[E] || (l.push(E), e[E] = !0);
            }
          }
        }
        s || this._config.onError(L);
      }

      _hasDependencyPath(c, o) {
        let L = this._modules2[c];
        if (!L) return !1;
        let e = [];
        for (let l = 0, u = this._moduleIdProvider.getMaxModuleId(); l < u; l++) e[l] = !1;
        let s = [];
        for (s.push(L), e[c] = !0; s.length > 0;) {
          let u = s.shift().dependencies;
          if (u) {
            for (let b = 0, f = u.length; b < f; b++) {
              let y = u[b];
              if (y.id === o) return !0;
              let w = this._modules2[y.id];
              w && !e[y.id] && (e[y.id] = !0, s.push(w));
            }
          }
        }
        return !1;
      }

      _findCyclePath(c, o, L) {
        if (c === o || L === 50) return [c];
        let e = this._modules2[c];
        if (!e) return null;
        let s = e.dependencies;
        if (s) {
          for (let l = 0, u = s.length; l < u; l++) {
            let b = this._findCyclePath(s[l].id, o, L + 1);
            if (b !== null) return b.push(c), b;
          }
        }
        return null;
      }

      _createRequire(c) {
        let o = (L, e, s) => this._relativeRequire(c, L, e, s);
        return o.toUrl = L => this._config.requireToUrl(c.resolveModule(L)), o.getStats = () => this.getLoaderEvents(), o.hasDependencyCycle = () => this._hasDependencyCycle, o.config = (L, e = !1) => {
          this.configure(L, e);
        }, o.__$__nodeRequire = q.global.nodeRequire, o;
      }

      _loadModule(c) {
        if (this._modules2[c] || this._knownModules2[c]) return;
        this._knownModules2[c] = !0;
        let o = this._moduleIdProvider.getStrModuleId(c),
          L = this._config.moduleIdToPaths(o),
          e = /^@[^\/]+\/[^\/]+$/;
        this._env.isNode && (o.indexOf('/') === -1 || e.test(o)) && L.push('node|' + o);
        let s = -1,
          l = u => {
            if (s++, s >= L.length) {
              this._onLoadError(c, u);
            } else {
              let b = L[s],
                f = this.getRecorder();
              if (this._config.isBuild() && b === 'empty:') {
                this._buildInfoPath[c] = b, this.defineModule(this._moduleIdProvider.getStrModuleId(c), [], null, null, null), this._onLoad(c);
                return;
              }
              f.record(10, b), this._scriptLoader.load(this, b, () => {
                this._config.isBuild() && (this._buildInfoPath[c] = b), f.record(11, b), this._onLoad(c);
              }, y => {
                f.record(12, b), l(y);
              });
            }
          };
        l(null);
      }

      _loadPluginDependency(c, o) {
        if (this._modules2[o.id] || this._knownModules2[o.id]) return;
        this._knownModules2[o.id] = !0;
        let L = e => {
          this.defineModule(this._moduleIdProvider.getStrModuleId(o.id), [], e, null, null);
        };
        L.error = e => {
          this._config.onError(this._createLoadError(o.id, e));
        }, c.load(o.pluginParam, this._createRequire(t.ROOT), L, this._config.getOptionsLiteral());
      }

      _resolve(c) {
        let o = c.dependencies;
        if (o) {
          for (let L = 0, e = o.length; L < e; L++) {
            let s = o[L];
            if (s === i.EXPORTS) {
              c.exportsPassedIn = !0, c.unresolvedDependenciesCount--;
              continue;
            }
            if (s === i.MODULE) {
              c.unresolvedDependenciesCount--;
              continue;
            }
            if (s === i.REQUIRE) {
              c.unresolvedDependenciesCount--;
              continue;
            }
            let l = this._modules2[s.id];
            if (l && l.isComplete()) {
              if (l.error) {
                c.onDependencyError(l.error);
                return;
              }
              c.unresolvedDependenciesCount--;
              continue;
            }
            if (this._hasDependencyPath(s.id, c.id)) {
              this._hasDependencyCycle = !0, console.warn('There is a dependency cycle between \'' + this._moduleIdProvider.getStrModuleId(s.id) + '\' and \'' + this._moduleIdProvider.getStrModuleId(c.id) + '\'. The cyclic path follows:');
              let u = this._findCyclePath(s.id, c.id, 0) || [];
              u.reverse(), u.push(s.id), console.warn(u.map(b => this._moduleIdProvider.getStrModuleId(b))
                .join(` => 
`)), c.unresolvedDependenciesCount--;
              continue;
            }
            if (this._inverseDependencies2[s.id] = this._inverseDependencies2[s.id] || [], this._inverseDependencies2[s.id].push(c.id), s instanceof d) {
              let u = this._modules2[s.pluginId];
              if (u && u.isComplete()) {
                this._loadPluginDependency(u.exports, s);
                continue;
              }
              let b = this._inversePluginDependencies2.get(s.pluginId);
              b || (b = [], this._inversePluginDependencies2.set(s.pluginId, b)), b.push(s), this._loadModule(s.pluginId);
              continue;
            }
            this._loadModule(s.id);
          }
        }
        c.unresolvedDependenciesCount === 0 && this._onModuleComplete(c);
      }

      _onModuleComplete(c) {
        let o = this.getRecorder();
        if (c.isComplete()) return;
        let L = c.dependencies,
          e = [];
        if (L) {
          for (let b = 0, f = L.length; b < f; b++) {
            let y = L[b];
            if (y === i.EXPORTS) {
              e[b] = c.exports;
              continue;
            }
            if (y === i.MODULE) {
              e[b] = {
                id: c.strId,
                config: () => this._config.getConfigForModule(c.strId),
              };
              continue;
            }
            if (y === i.REQUIRE) {
              e[b] = this._createRequire(c.moduleIdResolver);
              continue;
            }
            let w = this._modules2[y.id];
            if (w) {
              e[b] = w.exports;
              continue;
            }
            e[b] = null;
          }
        }
        const s = b => (this._inverseDependencies2[b] || []).map(f => this._moduleIdProvider.getStrModuleId(f));
        c.complete(o, this._config, e, s);
        let l = this._inverseDependencies2[c.id];
        if (this._inverseDependencies2[c.id] = null, l) {
          for (let b = 0, f = l.length; b < f; b++) {
            let y = l[b],
              w = this._modules2[y];
            w.unresolvedDependenciesCount--, w.unresolvedDependenciesCount === 0 && this._onModuleComplete(w);
          }
        }
        let u = this._inversePluginDependencies2.get(c.id);
        if (u) {
          this._inversePluginDependencies2.delete(c.id);
          for (let b = 0, f = u.length; b < f; b++) this._loadPluginDependency(c.exports, u[b]);
        }
      }
    }

    q.ModuleManager = _;
  })(ce || (ce = {}));
  var X,
    ce;
  (function(q) {
    const t = new q.Environment;
    let M = null;
    const R = function(p, c, o) {
      typeof p != 'string' && (o = c, c = p, p = null), (typeof c != 'object' || !Array.isArray(c)) && (o = c, c = null), c || (c = ['require', 'exports', 'module']), p ? M.defineModule(p, c, o, null, null) : M.enqueueDefineAnonymousModule(c, o);
    };
    R.amd = { jQuery: !0 };
    const i = function(p, c = !1) {
        M.configure(p, c);
      },
      d = function() {
        if (arguments.length === 1) {
          if (arguments[0] instanceof Object && !Array.isArray(arguments[0])) {
            i(arguments[0]);
            return;
          }
          if (typeof arguments[0] == 'string') return M.synchronousRequire(arguments[0]);
        }
        if ((arguments.length === 2 || arguments.length === 3) && Array.isArray(arguments[0])) {
          M.defineModule(q.Utilities.generateAnonymousModule(), arguments[0], arguments[1], arguments[2], null);
          return;
        }
        throw new Error('Unrecognized require call');
      };
    d.config = i, d.getConfig = function() {
      return M.getConfig()
        .getOptionsLiteral();
    }, d.reset = function() {
      M = M.reset();
    }, d.getBuildInfo = function() {
      return M.getBuildInfo();
    }, d.getStats = function() {
      return M.getLoaderEvents();
    }, d.define = R;

    function _() {
      if (typeof q.global.require < 'u' || typeof require < 'u') {
        const p = q.global.require || require;
        if (typeof p == 'function' && typeof p.resolve == 'function') {
          const c = q.ensureRecordedNodeRequire(M.getRecorder(), p);
          q.global.nodeRequire = c, d.nodeRequire = c, d.__$__nodeRequire = c;
        }
      }
      t.isNode && !t.isElectronRenderer && !t.isElectronNodeIntegrationWebWorker ? module.exports = d : (t.isElectronRenderer || (q.global.define = R), q.global.require = d);
    }

    q.init = _, (typeof q.global.define != 'function' || !q.global.define.amd) && (M = new q.ModuleManager(t, q.createScriptLoader(t), R, d, q.Utilities.getHighPerformanceTimestamp()), typeof q.global.require < 'u' && typeof q.global.require != 'function' && d.config(q.global.require), X = function() {
      return R.apply(null, arguments);
    }, X.amd = R.amd, typeof doNotInitLoader > 'u' && _());
  })(ce || (ce = {})), X(J[19], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.load = t.create = t.setPseudoTranslation = t.getConfiguredDefaultLocale = t.localize2 = t.localize = void 0;
    let M = typeof document < 'u' && document.location && document.location.hash.indexOf('pseudo=true') >= 0;
    const R = 'i-default';

    function i(f, y) {
      let w;
      return y.length === 0 ? w = f : w = f.replace(/\{(\d+)\}/g, (E, S) => {
        const C = S[0],
          r = y[C];
        let a = E;
        return typeof r == 'string' ? a = r : (typeof r == 'number' || typeof r == 'boolean' || r === void 0 || r === null) && (a = String(r)), a;
      }), M && (w = '\uFF3B' + w.replace(/[aouei]/g, '$&$&') + '\uFF3D'), w;
    }

    function d(f, y) {
      let w = f[y];
      return w || (w = f['*'], w) ? w : null;
    }

    function _(f) {
      return f.charAt(f.length - 1) === '/' ? f : f + '/';
    }

    async function p(f, y, w) {
      const E = _(f) + _(y) + 'vscode/' + _(w),
        S = await fetch(E);
      if (S.ok) return await S.json();
      throw new Error(`${S.status} - ${S.statusText}`);
    }

    function c(f) {
      return function(y, w) {
        const E = Array.prototype.slice.call(arguments, 2);
        return i(f[y], E);
      };
    }

    function o(f) {
      return (y, w, ...E) => ({
        value: i(f[y], E),
        original: i(w, E),
      });
    }

    function L(f, y, ...w) {
      return i(y, w);
    }

    t.localize = L;

    function e(f, y, ...w) {
      const E = i(y, w);
      return {
        value: E,
        original: E,
      };
    }

    t.localize2 = e;

    function s(f) {
    }

    t.getConfiguredDefaultLocale = s;

    function l(f) {
      M = f;
    }

    t.setPseudoTranslation = l;

    function u(f, y) {
      var w;
      return {
        localize: c(y[f]),
        localize2: o(y[f]),
        getConfiguredDefaultLocale: (w = y.getConfiguredDefaultLocale) !== null && w !== void 0 ? w : E => {
        },
      };
    }

    t.create = u;

    function b(f, y, w, E) {
      var S;
      const C = (S = E['vs/nls']) !== null && S !== void 0 ? S : {};
      if (!f || f.length === 0) {
        return w({
          localize: L,
          localize2: e,
          getConfiguredDefaultLocale: () => {
            var h;
            return (h = C.availableLanguages) === null || h === void 0 ? void 0 : h['*'];
          },
        });
      }
      const r = C.availableLanguages ? d(C.availableLanguages, f) : null,
        a = r === null || r === R;
      let g = '.nls';
      a || (g = g + '.' + r);
      const m = h => {
        Array.isArray(h) ? (h.localize = c(h), h.localize2 = o(h)) : (h.localize = c(h[f]), h.localize2 = o(h[f])), h.getConfiguredDefaultLocale = () => {
          var v;
          return (v = C.availableLanguages) === null || v === void 0 ? void 0 : v['*'];
        }, w(h);
      };
      typeof C.loadBundle == 'function' ? C.loadBundle(f, r, (h, v) => {
        h ? y([f + '.nls'], m) : m(v);
      }) : C.translationServiceUrl && !a ? (async () => {
        var h;
        try {
          const v = await p(C.translationServiceUrl, r, f);
          return m(v);
        } catch (v) {
          if (!r.includes('-')) return console.error(v), y([f + '.nls'], m);
          try {
            const N = r.split('-')[0],
              A = await p(C.translationServiceUrl, N, f);
            return (h = C.availableLanguages) !== null && h !== void 0 || (C.availableLanguages = {}), C.availableLanguages['*'] = N, m(A);
          } catch (N) {
            return console.error(N), y([f + '.nls'], m);
          }
        }
      })() : y([f + g], m, h => {
        if (g === '.nls') {
          console.error('Failed trying to load default language strings', h);
          return;
        }
        console.error(`Failed to load message bundle for language ${r}. Falling back to the default language:`, h), y([f + '.nls'], m);
      });
    }

    t.load = b;
  }), function() {
    const q = globalThis.MonacoEnvironment,
      t = q && q.baseUrl ? q.baseUrl : '../../../';

    function M(L, e) {
      var s;
      if (q?.createTrustedTypesPolicy) {
        try {
          return q.createTrustedTypesPolicy(L, e);
        } catch (l) {
          console.warn(l);
          return;
        }
      }
      try {
        return (s = self.trustedTypes) === null || s === void 0 ? void 0 : s.createPolicy(L, e);
      } catch (l) {
        console.warn(l);
        return;
      }
    }

    const R = M('amdLoader', {
      createScriptURL: L => L,
      createScript: (L, ...e) => {
        const s = e.slice(0, -1)
            .join(','),
          l = e.pop()
            .toString();
        return `(function anonymous(${s}) { ${l}
})`;
      },
    });

    function i() {
      try {
        return (R ? globalThis.eval(R.createScript('', 'true')) : new Function('true')).call(globalThis), !0;
      } catch {
        return !1;
      }
    }

    function d() {
      return new Promise((L, e) => {
        if (typeof globalThis.define == 'function' && globalThis.define.amd) return L();
        const s = t + 'vs/loader.js';
        if (!(/^((http:)|(https:)|(file:))/.test(s) && s.substring(0, globalThis.origin.length) !== globalThis.origin) && i()) {
          fetch(s)
            .then(u => {
              if (u.status !== 200) throw new Error(u.statusText);
              return u.text();
            })
            .then(u => {
              u = `${u}
//# sourceURL=${s}`, (R ? globalThis.eval(R.createScript('', u)) : new Function(u)).call(globalThis), L();
            })
            .then(void 0, e);
          return;
        }
        R ? importScripts(R.createScriptURL(s)) : importScripts(s), L();
      });
    }

    function _() {
      require.config({
        baseUrl: t,
        catchError: !0,
        trustedTypesPolicy: R,
        amdModulesPattern: /^vs\//,
      });
    }

    function p(L) {
      d()
        .then(() => {
          _(), require([L], function(e) {
            setTimeout(function() {
              const s = e.create((l, u) => {
                globalThis.postMessage(l, u);
              }, null);
              for (globalThis.onmessage = l => s.onmessage(l.data, l.ports); o.length > 0;) {
                const l = o.shift();
                s.onmessage(l.data, l.ports);
              }
            }, 0);
          });
        });
    }

    typeof globalThis.define == 'function' && globalThis.define.amd && _();
    let c = !0;
    const o = [];
    globalThis.onmessage = L => {
      if (!c) {
        o.push(L);
        return;
      }
      c = !1, p(L.data);
    };
  }(), X(J[7], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.CallbackIterable = t.ArrayQueue = t.reverseOrder = t.booleanComparator = t.numberComparator = t.tieBreakComparators = t.compareBy = t.CompareResult = t.splice = t.insertInto = t.asArray = t.pushMany = t.pushToEnd = t.pushToStart = t.arrayInsert = t.range = t.firstOrDefault = t.distinct = t.isNonEmptyArray = t.isFalsyOrEmpty = t.coalesceInPlace = t.coalesce = t.forEachWithNeighbors = t.forEachAdjacent = t.groupAdjacentBy = t.groupBy = t.quickSelect = t.binarySearch2 = t.binarySearch = t.removeFastWithoutKeepingOrder = t.equals = t.tail2 = t.tail = void 0;

    function M(x, O = 0) {
      return x[x.length - (1 + O)];
    }

    t.tail = M;

    function R(x) {
      if (x.length === 0) throw new Error('Invalid tail call');
      return [x.slice(0, x.length - 1), x[x.length - 1]];
    }

    t.tail2 = R;

    function i(x, O, F = (W, H) => W === H) {
      if (x === O) return !0;
      if (!x || !O || x.length !== O.length) return !1;
      for (let W = 0, H = x.length; W < H; W++) if (!F(x[W], O[W])) return !1;
      return !0;
    }

    t.equals = i;

    function d(x, O) {
      const F = x.length - 1;
      O < F && (x[O] = x[F]), x.pop();
    }

    t.removeFastWithoutKeepingOrder = d;

    function _(x, O, F) {
      return p(x.length, W => F(x[W], O));
    }

    t.binarySearch = _;

    function p(x, O) {
      let F = 0,
        W = x - 1;
      for (; F <= W;) {
        const H = (F + W) / 2 | 0,
          G = O(H);
        if (G < 0) F = H + 1; else if (G > 0) W = H - 1; else return H;
      }
      return -(F + 1);
    }

    t.binarySearch2 = p;

    function c(x, O, F) {
      if (x = x | 0, x >= O.length) throw new TypeError('invalid index');
      const W = O[Math.floor(O.length * Math.random())],
        H = [],
        G = [],
        ne = [];
      for (const se of O) {
        const n = F(se, W);
        n < 0 ? H.push(se) : n > 0 ? G.push(se) : ne.push(se);
      }
      return x < H.length ? c(x, H, F) : x < H.length + ne.length ? ne[0] : c(x - (H.length + ne.length), G, F);
    }

    t.quickSelect = c;

    function o(x, O) {
      const F = [];
      let W;
      for (const H of x.slice(0)
        .sort(O)) {
        !W || O(W[0], H) !== 0 ? (W = [H], F.push(W)) : W.push(H);
      }
      return F;
    }

    t.groupBy = o;

    function* L(x, O) {
      let F,
        W;
      for (const H of x) W !== void 0 && O(W, H) ? F.push(H) : (F && (yield F), F = [H]), W = H;
      F && (yield F);
    }

    t.groupAdjacentBy = L;

    function e(x, O) {
      for (let F = 0; F <= x.length; F++) O(F === 0 ? void 0 : x[F - 1], F === x.length ? void 0 : x[F]);
    }

    t.forEachAdjacent = e;

    function s(x, O) {
      for (let F = 0; F < x.length; F++) O(F === 0 ? void 0 : x[F - 1], x[F], F + 1 === x.length ? void 0 : x[F + 1]);
    }

    t.forEachWithNeighbors = s;

    function l(x) {
      return x.filter(O => !!O);
    }

    t.coalesce = l;

    function u(x) {
      let O = 0;
      for (let F = 0; F < x.length; F++) x[F] && (x[O] = x[F], O += 1);
      x.length = O;
    }

    t.coalesceInPlace = u;

    function b(x) {
      return !Array.isArray(x) || x.length === 0;
    }

    t.isFalsyOrEmpty = b;

    function f(x) {
      return Array.isArray(x) && x.length > 0;
    }

    t.isNonEmptyArray = f;

    function y(x, O = F => F) {
      const F = new Set;
      return x.filter(W => {
        const H = O(W);
        return F.has(H) ? !1 : (F.add(H), !0);
      });
    }

    t.distinct = y;

    function w(x, O) {
      return x.length > 0 ? x[0] : O;
    }

    t.firstOrDefault = w;

    function E(x, O) {
      let F = typeof O == 'number' ? x : 0;
      typeof O == 'number' ? F = x : (F = 0, O = x);
      const W = [];
      if (F <= O) for (let H = F; H < O; H++) W.push(H); else for (let H = F; H > O; H--) W.push(H);
      return W;
    }

    t.range = E;

    function S(x, O, F) {
      const W = x.slice(0, O),
        H = x.slice(O);
      return W.concat(F, H);
    }

    t.arrayInsert = S;

    function C(x, O) {
      const F = x.indexOf(O);
      F > -1 && (x.splice(F, 1), x.unshift(O));
    }

    t.pushToStart = C;

    function r(x, O) {
      const F = x.indexOf(O);
      F > -1 && (x.splice(F, 1), x.push(O));
    }

    t.pushToEnd = r;

    function a(x, O) {
      for (const F of O) x.push(F);
    }

    t.pushMany = a;

    function g(x) {
      return Array.isArray(x) ? x : [x];
    }

    t.asArray = g;

    function m(x, O, F) {
      const W = v(x, O),
        H = x.length,
        G = F.length;
      x.length = H + G;
      for (let ne = H - 1; ne >= W; ne--) x[ne + G] = x[ne];
      for (let ne = 0; ne < G; ne++) x[ne + W] = F[ne];
    }

    t.insertInto = m;

    function h(x, O, F, W) {
      const H = v(x, O);
      let G = x.splice(H, F);
      return G === void 0 && (G = []), m(x, H, W), G;
    }

    t.splice = h;

    function v(x, O) {
      return O < 0 ? Math.max(O + x.length, 0) : Math.min(O, x.length);
    }

    var N;
    (function(x) {
      function O(G) {
        return G < 0;
      }

      x.isLessThan = O;

      function F(G) {
        return G <= 0;
      }

      x.isLessThanOrEqual = F;

      function W(G) {
        return G > 0;
      }

      x.isGreaterThan = W;

      function H(G) {
        return G === 0;
      }

      x.isNeitherLessOrGreaterThan = H, x.greaterThan = 1, x.lessThan = -1, x.neitherLessOrGreaterThan = 0;
    })(N || (t.CompareResult = N = {}));

    function A(x, O) {
      return (F, W) => O(x(F), x(W));
    }

    t.compareBy = A;

    function D(...x) {
      return (O, F) => {
        for (const W of x) {
          const H = W(O, F);
          if (!N.isNeitherLessOrGreaterThan(H)) return H;
        }
        return N.neitherLessOrGreaterThan;
      };
    }

    t.tieBreakComparators = D;
    const P = (x, O) => x - O;
    t.numberComparator = P;
    const T = (x, O) => (0, t.numberComparator)(x ? 1 : 0, O ? 1 : 0);
    t.booleanComparator = T;

    function I(x) {
      return (O, F) => -x(O, F);
    }

    t.reverseOrder = I;

    class B {
      constructor(O) {
        this.items = O, this.firstIdx = 0, this.lastIdx = this.items.length - 1;
      }

      get length() {
        return this.lastIdx - this.firstIdx + 1;
      }

      takeWhile(O) {
        let F = this.firstIdx;
        for (; F < this.items.length && O(this.items[F]);) F++;
        const W = F === this.firstIdx ? null : this.items.slice(this.firstIdx, F);
        return this.firstIdx = F, W;
      }

      takeFromEndWhile(O) {
        let F = this.lastIdx;
        for (; F >= 0 && O(this.items[F]);) F--;
        const W = F === this.lastIdx ? null : this.items.slice(F + 1, this.lastIdx + 1);
        return this.lastIdx = F, W;
      }

      peek() {
        if (this.length !== 0) return this.items[this.firstIdx];
      }

      dequeue() {
        const O = this.items[this.firstIdx];
        return this.firstIdx++, O;
      }

      takeCount(O) {
        const F = this.items.slice(this.firstIdx, this.firstIdx + O);
        return this.firstIdx += O, F;
      }
    }

    t.ArrayQueue = B;

    class z {
      constructor(O) {
        this.iterate = O;
      }

      toArray() {
        const O = [];
        return this.iterate(F => (O.push(F), !0)), O;
      }

      filter(O) {
        return new z(F => this.iterate(W => O(W) ? F(W) : !0));
      }

      map(O) {
        return new z(F => this.iterate(W => F(O(W))));
      }

      findLast(O) {
        let F;
        return this.iterate(W => (O(W) && (F = W), !0)), F;
      }

      findLastMaxBy(O) {
        let F,
          W = !0;
        return this.iterate(H => ((W || N.isGreaterThan(O(H, F))) && (W = !1, F = H), !0)), F;
      }
    }

    t.CallbackIterable = z, z.empty = new z(x => {
    });
  }), X(J[11], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.mapFindFirst = t.findMaxIdxBy = t.findFirstMinBy = t.findLastMaxBy = t.findFirstMaxBy = t.MonotonousArray = t.findFirstIdxMonotonousOrArrLen = t.findFirstMonotonous = t.findLastIdxMonotonous = t.findLastMonotonous = t.findLastIdx = t.findLast = void 0;

    function M(u, b, f) {
      const y = R(u, b);
      if (y !== -1) return u[y];
    }

    t.findLast = M;

    function R(u, b, f = u.length - 1) {
      for (let y = f; y >= 0; y--) {
        const w = u[y];
        if (b(w)) return y;
      }
      return -1;
    }

    t.findLastIdx = R;

    function i(u, b) {
      const f = d(u, b);
      return f === -1 ? void 0 : u[f];
    }

    t.findLastMonotonous = i;

    function d(u, b, f = 0, y = u.length) {
      let w = f,
        E = y;
      for (; w < E;) {
        const S = Math.floor((w + E) / 2);
        b(u[S]) ? w = S + 1 : E = S;
      }
      return w - 1;
    }

    t.findLastIdxMonotonous = d;

    function _(u, b) {
      const f = p(u, b);
      return f === u.length ? void 0 : u[f];
    }

    t.findFirstMonotonous = _;

    function p(u, b, f = 0, y = u.length) {
      let w = f,
        E = y;
      for (; w < E;) {
        const S = Math.floor((w + E) / 2);
        b(u[S]) ? E = S : w = S + 1;
      }
      return w;
    }

    t.findFirstIdxMonotonousOrArrLen = p;

    class c {
      constructor(b) {
        this._array = b, this._findLastMonotonousLastIdx = 0;
      }

      findLastMonotonous(b) {
        if (c.assertInvariants) {
          if (this._prevFindLastPredicate) {
            for (const y of this._array) if (this._prevFindLastPredicate(y) && !b(y)) throw new Error('MonotonousArray: current predicate must be weaker than (or equal to) the previous predicate.');
          }
          this._prevFindLastPredicate = b;
        }
        const f = d(this._array, b, this._findLastMonotonousLastIdx);
        return this._findLastMonotonousLastIdx = f + 1, f === -1 ? void 0 : this._array[f];
      }
    }

    t.MonotonousArray = c, c.assertInvariants = !1;

    function o(u, b) {
      if (u.length === 0) return;
      let f = u[0];
      for (let y = 1; y < u.length; y++) {
        const w = u[y];
        b(w, f) > 0 && (f = w);
      }
      return f;
    }

    t.findFirstMaxBy = o;

    function L(u, b) {
      if (u.length === 0) return;
      let f = u[0];
      for (let y = 1; y < u.length; y++) {
        const w = u[y];
        b(w, f) >= 0 && (f = w);
      }
      return f;
    }

    t.findLastMaxBy = L;

    function e(u, b) {
      return o(u, (f, y) => -b(f, y));
    }

    t.findFirstMinBy = e;

    function s(u, b) {
      if (u.length === 0) return -1;
      let f = 0;
      for (let y = 1; y < u.length; y++) {
        const w = u[y];
        b(w, u[f]) > 0 && (f = y);
      }
      return f;
    }

    t.findMaxIdxBy = s;

    function l(u, b) {
      for (const f of u) {
        const y = b(f);
        if (y !== void 0) return y;
      }
    }

    t.mapFindFirst = l;
  }), X(J[32], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.CachedFunction = t.LRUCachedFunction = void 0;

    class M {
      constructor(d) {
        this.fn = d, this.lastCache = void 0, this.lastArgKey = void 0;
      }

      get(d) {
        const _ = JSON.stringify(d);
        return this.lastArgKey !== _ && (this.lastArgKey = _, this.lastCache = this.fn(d)), this.lastCache;
      }
    }

    t.LRUCachedFunction = M;

    class R {
      constructor(d) {
        this.fn = d, this._map = new Map;
      }

      get cachedValues() {
        return this._map;
      }

      get(d) {
        if (this._map.has(d)) return this._map.get(d);
        const _ = this.fn(d);
        return this._map.set(d, _), _;
      }
    }

    t.CachedFunction = R;
  }), X(J[33], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Color = t.HSVA = t.HSLA = t.RGBA = void 0;

    function M(p, c) {
      const o = Math.pow(10, c);
      return Math.round(p * o) / o;
    }

    class R {
      constructor(c, o, L, e = 1) {
        this._rgbaBrand = void 0, this.r = Math.min(255, Math.max(0, c)) | 0, this.g = Math.min(255, Math.max(0, o)) | 0, this.b = Math.min(255, Math.max(0, L)) | 0, this.a = M(Math.max(Math.min(1, e), 0), 3);
      }

      static equals(c, o) {
        return c.r === o.r && c.g === o.g && c.b === o.b && c.a === o.a;
      }
    }

    t.RGBA = R;

    class i {
      constructor(c, o, L, e) {
        this._hslaBrand = void 0, this.h = Math.max(Math.min(360, c), 0) | 0, this.s = M(Math.max(Math.min(1, o), 0), 3), this.l = M(Math.max(Math.min(1, L), 0), 3), this.a = M(Math.max(Math.min(1, e), 0), 3);
      }

      static equals(c, o) {
        return c.h === o.h && c.s === o.s && c.l === o.l && c.a === o.a;
      }

      static fromRGBA(c) {
        const o = c.r / 255,
          L = c.g / 255,
          e = c.b / 255,
          s = c.a,
          l = Math.max(o, L, e),
          u = Math.min(o, L, e);
        let b = 0,
          f = 0;
        const y = (u + l) / 2,
          w = l - u;
        if (w > 0) {
          switch (f = Math.min(y <= .5 ? w / (2 * y) : w / (2 - 2 * y), 1), l) {
            case o:
              b = (L - e) / w + (L < e ? 6 : 0);
              break;
            case L:
              b = (e - o) / w + 2;
              break;
            case e:
              b = (o - L) / w + 4;
              break;
          }
          b *= 60, b = Math.round(b);
        }
        return new i(b, f, y, s);
      }

      static _hue2rgb(c, o, L) {
        return L < 0 && (L += 1), L > 1 && (L -= 1), L < 1 / 6 ? c + (o - c) * 6 * L : L < 1 / 2 ? o : L < 2 / 3 ? c + (o - c) * (2 / 3 - L) * 6 : c;
      }

      static toRGBA(c) {
        const o = c.h / 360, {
          s: L,
          l: e,
          a: s,
        } = c;
        let l,
          u,
          b;
        if (L === 0) {
          l = u = b = e;
        } else {
          const f = e < .5 ? e * (1 + L) : e + L - e * L,
            y = 2 * e - f;
          l = i._hue2rgb(y, f, o + 1 / 3), u = i._hue2rgb(y, f, o), b = i._hue2rgb(y, f, o - 1 / 3);
        }
        return new R(Math.round(l * 255), Math.round(u * 255), Math.round(b * 255), s);
      }
    }

    t.HSLA = i;

    class d {
      constructor(c, o, L, e) {
        this._hsvaBrand = void 0, this.h = Math.max(Math.min(360, c), 0) | 0, this.s = M(Math.max(Math.min(1, o), 0), 3), this.v = M(Math.max(Math.min(1, L), 0), 3), this.a = M(Math.max(Math.min(1, e), 0), 3);
      }

      static equals(c, o) {
        return c.h === o.h && c.s === o.s && c.v === o.v && c.a === o.a;
      }

      static fromRGBA(c) {
        const o = c.r / 255,
          L = c.g / 255,
          e = c.b / 255,
          s = Math.max(o, L, e),
          l = Math.min(o, L, e),
          u = s - l,
          b = s === 0 ? 0 : u / s;
        let f;
        return u === 0 ? f = 0 : s === o ? f = ((L - e) / u % 6 + 6) % 6 : s === L ? f = (e - o) / u + 2 : f = (o - L) / u + 4, new d(Math.round(f * 60), b, s, c.a);
      }

      static toRGBA(c) {
        const {
            h: o,
            s: L,
            v: e,
            a: s,
          } = c,
          l = e * L,
          u = l * (1 - Math.abs(o / 60 % 2 - 1)),
          b = e - l;
        let [f, y, w] = [0, 0, 0];
        return o < 60 ? (f = l, y = u) : o < 120 ? (f = u, y = l) : o < 180 ? (y = l, w = u) : o < 240 ? (y = u, w = l) : o < 300 ? (f = u, w = l) : o <= 360 && (f = l, w = u), f = Math.round((f + b) * 255), y = Math.round((y + b) * 255), w = Math.round((w + b) * 255), new R(f, y, w, s);
      }
    }

    t.HSVA = d;

    class _ {
      constructor(c) {
        if (c) if (c instanceof R) this.rgba = c; else if (c instanceof i) this._hsla = c, this.rgba = i.toRGBA(c); else if (c instanceof d) this._hsva = c, this.rgba = d.toRGBA(c); else throw new Error('Invalid color ctor argument'); else throw new Error('Color needs a value');
      }

      get hsla() {
        return this._hsla ? this._hsla : i.fromRGBA(this.rgba);
      }

      get hsva() {
        return this._hsva ? this._hsva : d.fromRGBA(this.rgba);
      }

      static fromHex(c) {
        return _.Format.CSS.parseHex(c) || _.red;
      }

      static equals(c, o) {
        return !c && !o ? !0 : !c || !o ? !1 : c.equals(o);
      }

      static _relativeLuminanceForComponent(c) {
        const o = c / 255;
        return o <= .03928 ? o / 12.92 : Math.pow((o + .055) / 1.055, 2.4);
      }

      static getLighterColor(c, o, L) {
        if (c.isLighterThan(o)) return c;
        L = L || .5;
        const e = c.getRelativeLuminance(),
          s = o.getRelativeLuminance();
        return L = L * (s - e) / s, c.lighten(L);
      }

      static getDarkerColor(c, o, L) {
        if (c.isDarkerThan(o)) return c;
        L = L || .5;
        const e = c.getRelativeLuminance(),
          s = o.getRelativeLuminance();
        return L = L * (e - s) / e, c.darken(L);
      }

      equals(c) {
        return !!c && R.equals(this.rgba, c.rgba) && i.equals(this.hsla, c.hsla) && d.equals(this.hsva, c.hsva);
      }

      getRelativeLuminance() {
        const c = _._relativeLuminanceForComponent(this.rgba.r),
          o = _._relativeLuminanceForComponent(this.rgba.g),
          L = _._relativeLuminanceForComponent(this.rgba.b),
          e = .2126 * c + .7152 * o + .0722 * L;
        return M(e, 4);
      }

      isLighter() {
        return (this.rgba.r * 299 + this.rgba.g * 587 + this.rgba.b * 114) / 1e3 >= 128;
      }

      isLighterThan(c) {
        const o = this.getRelativeLuminance(),
          L = c.getRelativeLuminance();
        return o > L;
      }

      isDarkerThan(c) {
        const o = this.getRelativeLuminance(),
          L = c.getRelativeLuminance();
        return o < L;
      }

      lighten(c) {
        return new _(new i(this.hsla.h, this.hsla.s, this.hsla.l + this.hsla.l * c, this.hsla.a));
      }

      darken(c) {
        return new _(new i(this.hsla.h, this.hsla.s, this.hsla.l - this.hsla.l * c, this.hsla.a));
      }

      transparent(c) {
        const {
          r: o,
          g: L,
          b: e,
          a: s,
        } = this.rgba;
        return new _(new R(o, L, e, s * c));
      }

      isTransparent() {
        return this.rgba.a === 0;
      }

      isOpaque() {
        return this.rgba.a === 1;
      }

      opposite() {
        return new _(new R(255 - this.rgba.r, 255 - this.rgba.g, 255 - this.rgba.b, this.rgba.a));
      }

      makeOpaque(c) {
        if (this.isOpaque() || c.rgba.a !== 1) return this;
        const {
          r: o,
          g: L,
          b: e,
          a: s,
        } = this.rgba;
        return new _(new R(c.rgba.r - s * (c.rgba.r - o), c.rgba.g - s * (c.rgba.g - L), c.rgba.b - s * (c.rgba.b - e), 1));
      }

      toString() {
        return this._toString || (this._toString = _.Format.CSS.format(this)), this._toString;
      }
    }

    t.Color = _, _.white = new _(new R(255, 255, 255, 1)), _.black = new _(new R(0, 0, 0, 1)), _.red = new _(new R(255, 0, 0, 1)), _.blue = new _(new R(0, 0, 255, 1)), _.green = new _(new R(0, 255, 0, 1)), _.cyan = new _(new R(0, 255, 255, 1)), _.lightgrey = new _(new R(211, 211, 211, 1)), _.transparent = new _(new R(0, 0, 0, 0)), function(p) {
      let c;
      (function(o) {
        let L;
        (function(e) {
          function s(r) {
            return r.rgba.a === 1 ? `rgb(${r.rgba.r}, ${r.rgba.g}, ${r.rgba.b})` : p.Format.CSS.formatRGBA(r);
          }

          e.formatRGB = s;

          function l(r) {
            return `rgba(${r.rgba.r}, ${r.rgba.g}, ${r.rgba.b}, ${+r.rgba.a.toFixed(2)})`;
          }

          e.formatRGBA = l;

          function u(r) {
            return r.hsla.a === 1 ? `hsl(${r.hsla.h}, ${(r.hsla.s * 100).toFixed(2)}%, ${(r.hsla.l * 100).toFixed(2)}%)` : p.Format.CSS.formatHSLA(r);
          }

          e.formatHSL = u;

          function b(r) {
            return `hsla(${r.hsla.h}, ${(r.hsla.s * 100).toFixed(2)}%, ${(r.hsla.l * 100).toFixed(2)}%, ${r.hsla.a.toFixed(2)})`;
          }

          e.formatHSLA = b;

          function f(r) {
            const a = r.toString(16);
            return a.length !== 2 ? '0' + a : a;
          }

          function y(r) {
            return `#${f(r.rgba.r)}${f(r.rgba.g)}${f(r.rgba.b)}`;
          }

          e.formatHex = y;

          function w(r, a = !1) {
            return a && r.rgba.a === 1 ? p.Format.CSS.formatHex(r) : `#${f(r.rgba.r)}${f(r.rgba.g)}${f(r.rgba.b)}${f(Math.round(r.rgba.a * 255))}`;
          }

          e.formatHexA = w;

          function E(r) {
            return r.isOpaque() ? p.Format.CSS.formatHex(r) : p.Format.CSS.formatRGBA(r);
          }

          e.format = E;

          function S(r) {
            const a = r.length;
            if (a === 0 || r.charCodeAt(0) !== 35) return null;
            if (a === 7) {
              const g = 16 * C(r.charCodeAt(1)) + C(r.charCodeAt(2)),
                m = 16 * C(r.charCodeAt(3)) + C(r.charCodeAt(4)),
                h = 16 * C(r.charCodeAt(5)) + C(r.charCodeAt(6));
              return new p(new R(g, m, h, 1));
            }
            if (a === 9) {
              const g = 16 * C(r.charCodeAt(1)) + C(r.charCodeAt(2)),
                m = 16 * C(r.charCodeAt(3)) + C(r.charCodeAt(4)),
                h = 16 * C(r.charCodeAt(5)) + C(r.charCodeAt(6)),
                v = 16 * C(r.charCodeAt(7)) + C(r.charCodeAt(8));
              return new p(new R(g, m, h, v / 255));
            }
            if (a === 4) {
              const g = C(r.charCodeAt(1)),
                m = C(r.charCodeAt(2)),
                h = C(r.charCodeAt(3));
              return new p(new R(16 * g + g, 16 * m + m, 16 * h + h));
            }
            if (a === 5) {
              const g = C(r.charCodeAt(1)),
                m = C(r.charCodeAt(2)),
                h = C(r.charCodeAt(3)),
                v = C(r.charCodeAt(4));
              return new p(new R(16 * g + g, 16 * m + m, 16 * h + h, (16 * v + v) / 255));
            }
            return null;
          }

          e.parseHex = S;

          function C(r) {
            switch (r) {
              case 48:
                return 0;
              case 49:
                return 1;
              case 50:
                return 2;
              case 51:
                return 3;
              case 52:
                return 4;
              case 53:
                return 5;
              case 54:
                return 6;
              case 55:
                return 7;
              case 56:
                return 8;
              case 57:
                return 9;
              case 97:
                return 10;
              case 65:
                return 10;
              case 98:
                return 11;
              case 66:
                return 11;
              case 99:
                return 12;
              case 67:
                return 12;
              case 100:
                return 13;
              case 68:
                return 13;
              case 101:
                return 14;
              case 69:
                return 14;
              case 102:
                return 15;
              case 70:
                return 15;
            }
            return 0;
          }
        })(L = o.CSS || (o.CSS = {}));
      })(c = p.Format || (p.Format = {}));
    }(_ || (t.Color = _ = {}));
  }), X(J[34], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.DiffChange = void 0;

    class M {
      constructor(i, d, _, p) {
        this.originalStart = i, this.originalLength = d, this.modifiedStart = _, this.modifiedLength = p;
      }

      getOriginalEnd() {
        return this.originalStart + this.originalLength;
      }

      getModifiedEnd() {
        return this.modifiedStart + this.modifiedLength;
      }
    }

    t.DiffChange = M;
  }), X(J[5], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.BugIndicatingError = t.ErrorNoTelemetry = t.NotSupportedError = t.illegalState = t.illegalArgument = t.canceled = t.CancellationError = t.isCancellationError = t.transformErrorForSerialization = t.onUnexpectedExternalError = t.onUnexpectedError = t.errorHandler = t.ErrorHandler = void 0;

    class M {
      constructor() {
        this.listeners = [], this.unexpectedErrorHandler = function(f) {
          setTimeout(() => {
            throw f.stack ? l.isErrorNoTelemetry(f) ? new l(f.message + `

` + f.stack) : new Error(f.message + `

` + f.stack) : f;
          }, 0);
        };
      }

      emit(f) {
        this.listeners.forEach(y => {
          y(f);
        });
      }

      onUnexpectedError(f) {
        this.unexpectedErrorHandler(f), this.emit(f);
      }

      onUnexpectedExternalError(f) {
        this.unexpectedErrorHandler(f);
      }
    }

    t.ErrorHandler = M, t.errorHandler = new M;

    function R(b) {
      p(b) || t.errorHandler.onUnexpectedError(b);
    }

    t.onUnexpectedError = R;

    function i(b) {
      p(b) || t.errorHandler.onUnexpectedExternalError(b);
    }

    t.onUnexpectedExternalError = i;

    function d(b) {
      if (b instanceof Error) {
        const {
            name: f,
            message: y,
          } = b,
          w = b.stacktrace || b.stack;
        return {
          $isError: !0,
          name: f,
          message: y,
          stack: w,
          noTelemetry: l.isErrorNoTelemetry(b),
        };
      }
      return b;
    }

    t.transformErrorForSerialization = d;
    const _ = 'Canceled';

    function p(b) {
      return b instanceof c ? !0 : b instanceof Error && b.name === _ && b.message === _;
    }

    t.isCancellationError = p;

    class c extends Error {
      constructor() {
        super(_), this.name = this.message;
      }
    }

    t.CancellationError = c;

    function o() {
      const b = new Error(_);
      return b.name = b.message, b;
    }

    t.canceled = o;

    function L(b) {
      return b ? new Error(`Illegal argument: ${b}`) : new Error('Illegal argument');
    }

    t.illegalArgument = L;

    function e(b) {
      return b ? new Error(`Illegal state: ${b}`) : new Error('Illegal state');
    }

    t.illegalState = e;

    class s extends Error {
      constructor(f) {
        super('NotSupported'), f && (this.message = f);
      }
    }

    t.NotSupportedError = s;

    class l extends Error {
      constructor(f) {
        super(f), this.name = 'CodeExpectedError';
      }

      static fromError(f) {
        if (f instanceof l) return f;
        const y = new l;
        return y.message = f.message, y.stack = f.stack, y;
      }

      static isErrorNoTelemetry(f) {
        return f.name === 'CodeExpectedError';
      }
    }

    t.ErrorNoTelemetry = l;

    class u extends Error {
      constructor(f) {
        super(f || 'An unexpected bug occurred.'), Object.setPrototypeOf(this, u.prototype);
      }
    }

    t.BugIndicatingError = u;
  }), X(J[12], Z([0, 1, 5]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.checkAdjacentItems = t.assertFn = t.softAssert = t.assertNever = t.ok = void 0;

    function R(c, o) {
      if (!c) throw new Error(o ? `Assertion failed (${o})` : 'Assertion Failed');
    }

    t.ok = R;

    function i(c, o = 'Unreachable') {
      throw new Error(o);
    }

    t.assertNever = i;

    function d(c) {
      c || (0, M.onUnexpectedError)(new M.BugIndicatingError('Soft Assertion Failed'));
    }

    t.softAssert = d;

    function _(c) {
      if (!c()) {
        debugger;
        c(), (0, M.onUnexpectedError)(new M.BugIndicatingError('Assertion Failed'));
      }
    }

    t.assertFn = _;

    function p(c, o) {
      let L = 0;
      for (; L < c.length - 1;) {
        const e = c[L],
          s = c[L + 1];
        if (!o(e, s)) return !1;
        L++;
      }
      return !0;
    }

    t.checkAdjacentItems = p;
  }), X(J[20], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.createSingleCallFunction = void 0;

    function M(R, i) {
      const d = this;
      let _ = !1,
        p;
      return function() {
        if (_) return p;
        if (_ = !0, i) {
          try {
            p = R.apply(d, arguments);
          } finally {
            i();
          }
        } else {
          p = R.apply(d, arguments);
        }
        return p;
      };
    }

    t.createSingleCallFunction = M;
  }), X(J[21], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Iterable = void 0;
    var M;
    (function(R) {
      function i(r) {
        return r && typeof r == 'object' && typeof r[Symbol.iterator] == 'function';
      }

      R.is = i;
      const d = Object.freeze([]);

      function _() {
        return d;
      }

      R.empty = _;

      function* p(r) {
        yield r;
      }

      R.single = p;

      function c(r) {
        return i(r) ? r : p(r);
      }

      R.wrap = c;

      function o(r) {
        return r || d;
      }

      R.from = o;

      function* L(r) {
        for (let a = r.length - 1; a >= 0; a--) yield r[a];
      }

      R.reverse = L;

      function e(r) {
        return !r || r[Symbol.iterator]()
          .next().done === !0;
      }

      R.isEmpty = e;

      function s(r) {
        return r[Symbol.iterator]()
          .next().value;
      }

      R.first = s;

      function l(r, a) {
        for (const g of r) if (a(g)) return !0;
        return !1;
      }

      R.some = l;

      function u(r, a) {
        for (const g of r) if (a(g)) return g;
      }

      R.find = u;

      function* b(r, a) {
        for (const g of r) a(g) && (yield g);
      }

      R.filter = b;

      function* f(r, a) {
        let g = 0;
        for (const m of r) yield a(m, g++);
      }

      R.map = f;

      function* y(...r) {
        for (const a of r) yield* a;
      }

      R.concat = y;

      function w(r, a, g) {
        let m = g;
        for (const h of r) m = a(m, h);
        return m;
      }

      R.reduce = w;

      function* E(r, a, g = r.length) {
        for (a < 0 && (a += r.length), g < 0 ? g += r.length : g > r.length && (g = r.length); a < g; a++) yield r[a];
      }

      R.slice = E;

      function S(r, a = Number.POSITIVE_INFINITY) {
        const g = [];
        if (a === 0) return [g, r];
        const m = r[Symbol.iterator]();
        for (let h = 0; h < a; h++) {
          const v = m.next();
          if (v.done) return [g, R.empty()];
          g.push(v.value);
        }
        return [g, {
          [Symbol.iterator]() {
            return m;
          },
        }];
      }

      R.consume = S;

      async function C(r) {
        const a = [];
        for await(const g of r) a.push(g);
        return Promise.resolve(a);
      }

      R.asyncToArray = C;
    })(M || (t.Iterable = M = {}));
  }), X(J[35], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.KeyChord = t.KeyCodeUtils = t.IMMUTABLE_KEY_CODE_TO_CODE = t.IMMUTABLE_CODE_TO_KEY_CODE = t.NATIVE_WINDOWS_KEY_CODE_TO_KEY_CODE = t.EVENT_KEY_CODE_MAP = void 0;

    class M {
      constructor() {
        this._keyCodeToStr = [], this._strToKeyCode = Object.create(null);
      }

      define(s, l) {
        this._keyCodeToStr[s] = l, this._strToKeyCode[l.toLowerCase()] = s;
      }

      keyCodeToStr(s) {
        return this._keyCodeToStr[s];
      }

      strToKeyCode(s) {
        return this._strToKeyCode[s.toLowerCase()] || 0;
      }
    }

    const R = new M,
      i = new M,
      d = new M;
    t.EVENT_KEY_CODE_MAP = new Array(230), t.NATIVE_WINDOWS_KEY_CODE_TO_KEY_CODE = {};
    const _ = [],
      p = Object.create(null),
      c = Object.create(null);
    t.IMMUTABLE_CODE_TO_KEY_CODE = [], t.IMMUTABLE_KEY_CODE_TO_CODE = [];
    for (let e = 0; e <= 193; e++) t.IMMUTABLE_CODE_TO_KEY_CODE[e] = -1;
    for (let e = 0; e <= 132; e++) t.IMMUTABLE_KEY_CODE_TO_CODE[e] = -1;
    (function() {
      const e = '',
        s = [[1, 0, 'None', 0, 'unknown', 0, 'VK_UNKNOWN', e, e], [1, 1, 'Hyper', 0, e, 0, e, e, e], [1, 2, 'Super', 0, e, 0, e, e, e], [1, 3, 'Fn', 0, e, 0, e, e, e], [1, 4, 'FnLock', 0, e, 0, e, e, e], [1, 5, 'Suspend', 0, e, 0, e, e, e], [1, 6, 'Resume', 0, e, 0, e, e, e], [1, 7, 'Turbo', 0, e, 0, e, e, e], [1, 8, 'Sleep', 0, e, 0, 'VK_SLEEP', e, e], [1, 9, 'WakeUp', 0, e, 0, e, e, e], [0, 10, 'KeyA', 31, 'A', 65, 'VK_A', e, e], [0, 11, 'KeyB', 32, 'B', 66, 'VK_B', e, e], [0, 12, 'KeyC', 33, 'C', 67, 'VK_C', e, e], [0, 13, 'KeyD', 34, 'D', 68, 'VK_D', e, e], [0, 14, 'KeyE', 35, 'E', 69, 'VK_E', e, e], [0, 15, 'KeyF', 36, 'F', 70, 'VK_F', e, e], [0, 16, 'KeyG', 37, 'G', 71, 'VK_G', e, e], [0, 17, 'KeyH', 38, 'H', 72, 'VK_H', e, e], [0, 18, 'KeyI', 39, 'I', 73, 'VK_I', e, e], [0, 19, 'KeyJ', 40, 'J', 74, 'VK_J', e, e], [0, 20, 'KeyK', 41, 'K', 75, 'VK_K', e, e], [0, 21, 'KeyL', 42, 'L', 76, 'VK_L', e, e], [0, 22, 'KeyM', 43, 'M', 77, 'VK_M', e, e], [0, 23, 'KeyN', 44, 'N', 78, 'VK_N', e, e], [0, 24, 'KeyO', 45, 'O', 79, 'VK_O', e, e], [0, 25, 'KeyP', 46, 'P', 80, 'VK_P', e, e], [0, 26, 'KeyQ', 47, 'Q', 81, 'VK_Q', e, e], [0, 27, 'KeyR', 48, 'R', 82, 'VK_R', e, e], [0, 28, 'KeyS', 49, 'S', 83, 'VK_S', e, e], [0, 29, 'KeyT', 50, 'T', 84, 'VK_T', e, e], [0, 30, 'KeyU', 51, 'U', 85, 'VK_U', e, e], [0, 31, 'KeyV', 52, 'V', 86, 'VK_V', e, e], [0, 32, 'KeyW', 53, 'W', 87, 'VK_W', e, e], [0, 33, 'KeyX', 54, 'X', 88, 'VK_X', e, e], [0, 34, 'KeyY', 55, 'Y', 89, 'VK_Y', e, e], [0, 35, 'KeyZ', 56, 'Z', 90, 'VK_Z', e, e], [0, 36, 'Digit1', 22, '1', 49, 'VK_1', e, e], [0, 37, 'Digit2', 23, '2', 50, 'VK_2', e, e], [0, 38, 'Digit3', 24, '3', 51, 'VK_3', e, e], [0, 39, 'Digit4', 25, '4', 52, 'VK_4', e, e], [0, 40, 'Digit5', 26, '5', 53, 'VK_5', e, e], [0, 41, 'Digit6', 27, '6', 54, 'VK_6', e, e], [0, 42, 'Digit7', 28, '7', 55, 'VK_7', e, e], [0, 43, 'Digit8', 29, '8', 56, 'VK_8', e, e], [0, 44, 'Digit9', 30, '9', 57, 'VK_9', e, e], [0, 45, 'Digit0', 21, '0', 48, 'VK_0', e, e], [1, 46, 'Enter', 3, 'Enter', 13, 'VK_RETURN', e, e], [1, 47, 'Escape', 9, 'Escape', 27, 'VK_ESCAPE', e, e], [1, 48, 'Backspace', 1, 'Backspace', 8, 'VK_BACK', e, e], [1, 49, 'Tab', 2, 'Tab', 9, 'VK_TAB', e, e], [1, 50, 'Space', 10, 'Space', 32, 'VK_SPACE', e, e], [0, 51, 'Minus', 88, '-', 189, 'VK_OEM_MINUS', '-', 'OEM_MINUS'], [0, 52, 'Equal', 86, '=', 187, 'VK_OEM_PLUS', '=', 'OEM_PLUS'], [0, 53, 'BracketLeft', 92, '[', 219, 'VK_OEM_4', '[', 'OEM_4'], [0, 54, 'BracketRight', 94, ']', 221, 'VK_OEM_6', ']', 'OEM_6'], [0, 55, 'Backslash', 93, '\\', 220, 'VK_OEM_5', '\\', 'OEM_5'], [0, 56, 'IntlHash', 0, e, 0, e, e, e], [0, 57, 'Semicolon', 85, ';', 186, 'VK_OEM_1', ';', 'OEM_1'], [0, 58, 'Quote', 95, '\'', 222, 'VK_OEM_7', '\'', 'OEM_7'], [0, 59, 'Backquote', 91, '`', 192, 'VK_OEM_3', '`', 'OEM_3'], [0, 60, 'Comma', 87, ',', 188, 'VK_OEM_COMMA', ',', 'OEM_COMMA'], [0, 61, 'Period', 89, '.', 190, 'VK_OEM_PERIOD', '.', 'OEM_PERIOD'], [0, 62, 'Slash', 90, '/', 191, 'VK_OEM_2', '/', 'OEM_2'], [1, 63, 'CapsLock', 8, 'CapsLock', 20, 'VK_CAPITAL', e, e], [1, 64, 'F1', 59, 'F1', 112, 'VK_F1', e, e], [1, 65, 'F2', 60, 'F2', 113, 'VK_F2', e, e], [1, 66, 'F3', 61, 'F3', 114, 'VK_F3', e, e], [1, 67, 'F4', 62, 'F4', 115, 'VK_F4', e, e], [1, 68, 'F5', 63, 'F5', 116, 'VK_F5', e, e], [1, 69, 'F6', 64, 'F6', 117, 'VK_F6', e, e], [1, 70, 'F7', 65, 'F7', 118, 'VK_F7', e, e], [1, 71, 'F8', 66, 'F8', 119, 'VK_F8', e, e], [1, 72, 'F9', 67, 'F9', 120, 'VK_F9', e, e], [1, 73, 'F10', 68, 'F10', 121, 'VK_F10', e, e], [1, 74, 'F11', 69, 'F11', 122, 'VK_F11', e, e], [1, 75, 'F12', 70, 'F12', 123, 'VK_F12', e, e], [1, 76, 'PrintScreen', 0, e, 0, e, e, e], [1, 77, 'ScrollLock', 84, 'ScrollLock', 145, 'VK_SCROLL', e, e], [1, 78, 'Pause', 7, 'PauseBreak', 19, 'VK_PAUSE', e, e], [1, 79, 'Insert', 19, 'Insert', 45, 'VK_INSERT', e, e], [1, 80, 'Home', 14, 'Home', 36, 'VK_HOME', e, e], [1, 81, 'PageUp', 11, 'PageUp', 33, 'VK_PRIOR', e, e], [1, 82, 'Delete', 20, 'Delete', 46, 'VK_DELETE', e, e], [1, 83, 'End', 13, 'End', 35, 'VK_END', e, e], [1, 84, 'PageDown', 12, 'PageDown', 34, 'VK_NEXT', e, e], [1, 85, 'ArrowRight', 17, 'RightArrow', 39, 'VK_RIGHT', 'Right', e], [1, 86, 'ArrowLeft', 15, 'LeftArrow', 37, 'VK_LEFT', 'Left', e], [1, 87, 'ArrowDown', 18, 'DownArrow', 40, 'VK_DOWN', 'Down', e], [1, 88, 'ArrowUp', 16, 'UpArrow', 38, 'VK_UP', 'Up', e], [1, 89, 'NumLock', 83, 'NumLock', 144, 'VK_NUMLOCK', e, e], [1, 90, 'NumpadDivide', 113, 'NumPad_Divide', 111, 'VK_DIVIDE', e, e], [1, 91, 'NumpadMultiply', 108, 'NumPad_Multiply', 106, 'VK_MULTIPLY', e, e], [1, 92, 'NumpadSubtract', 111, 'NumPad_Subtract', 109, 'VK_SUBTRACT', e, e], [1, 93, 'NumpadAdd', 109, 'NumPad_Add', 107, 'VK_ADD', e, e], [1, 94, 'NumpadEnter', 3, e, 0, e, e, e], [1, 95, 'Numpad1', 99, 'NumPad1', 97, 'VK_NUMPAD1', e, e], [1, 96, 'Numpad2', 100, 'NumPad2', 98, 'VK_NUMPAD2', e, e], [1, 97, 'Numpad3', 101, 'NumPad3', 99, 'VK_NUMPAD3', e, e], [1, 98, 'Numpad4', 102, 'NumPad4', 100, 'VK_NUMPAD4', e, e], [1, 99, 'Numpad5', 103, 'NumPad5', 101, 'VK_NUMPAD5', e, e], [1, 100, 'Numpad6', 104, 'NumPad6', 102, 'VK_NUMPAD6', e, e], [1, 101, 'Numpad7', 105, 'NumPad7', 103, 'VK_NUMPAD7', e, e], [1, 102, 'Numpad8', 106, 'NumPad8', 104, 'VK_NUMPAD8', e, e], [1, 103, 'Numpad9', 107, 'NumPad9', 105, 'VK_NUMPAD9', e, e], [1, 104, 'Numpad0', 98, 'NumPad0', 96, 'VK_NUMPAD0', e, e], [1, 105, 'NumpadDecimal', 112, 'NumPad_Decimal', 110, 'VK_DECIMAL', e, e], [0, 106, 'IntlBackslash', 97, 'OEM_102', 226, 'VK_OEM_102', e, e], [1, 107, 'ContextMenu', 58, 'ContextMenu', 93, e, e, e], [1, 108, 'Power', 0, e, 0, e, e, e], [1, 109, 'NumpadEqual', 0, e, 0, e, e, e], [1, 110, 'F13', 71, 'F13', 124, 'VK_F13', e, e], [1, 111, 'F14', 72, 'F14', 125, 'VK_F14', e, e], [1, 112, 'F15', 73, 'F15', 126, 'VK_F15', e, e], [1, 113, 'F16', 74, 'F16', 127, 'VK_F16', e, e], [1, 114, 'F17', 75, 'F17', 128, 'VK_F17', e, e], [1, 115, 'F18', 76, 'F18', 129, 'VK_F18', e, e], [1, 116, 'F19', 77, 'F19', 130, 'VK_F19', e, e], [1, 117, 'F20', 78, 'F20', 131, 'VK_F20', e, e], [1, 118, 'F21', 79, 'F21', 132, 'VK_F21', e, e], [1, 119, 'F22', 80, 'F22', 133, 'VK_F22', e, e], [1, 120, 'F23', 81, 'F23', 134, 'VK_F23', e, e], [1, 121, 'F24', 82, 'F24', 135, 'VK_F24', e, e], [1, 122, 'Open', 0, e, 0, e, e, e], [1, 123, 'Help', 0, e, 0, e, e, e], [1, 124, 'Select', 0, e, 0, e, e, e], [1, 125, 'Again', 0, e, 0, e, e, e], [1, 126, 'Undo', 0, e, 0, e, e, e], [1, 127, 'Cut', 0, e, 0, e, e, e], [1, 128, 'Copy', 0, e, 0, e, e, e], [1, 129, 'Paste', 0, e, 0, e, e, e], [1, 130, 'Find', 0, e, 0, e, e, e], [1, 131, 'AudioVolumeMute', 117, 'AudioVolumeMute', 173, 'VK_VOLUME_MUTE', e, e], [1, 132, 'AudioVolumeUp', 118, 'AudioVolumeUp', 175, 'VK_VOLUME_UP', e, e], [1, 133, 'AudioVolumeDown', 119, 'AudioVolumeDown', 174, 'VK_VOLUME_DOWN', e, e], [1, 134, 'NumpadComma', 110, 'NumPad_Separator', 108, 'VK_SEPARATOR', e, e], [0, 135, 'IntlRo', 115, 'ABNT_C1', 193, 'VK_ABNT_C1', e, e], [1, 136, 'KanaMode', 0, e, 0, e, e, e], [0, 137, 'IntlYen', 0, e, 0, e, e, e], [1, 138, 'Convert', 0, e, 0, e, e, e], [1, 139, 'NonConvert', 0, e, 0, e, e, e], [1, 140, 'Lang1', 0, e, 0, e, e, e], [1, 141, 'Lang2', 0, e, 0, e, e, e], [1, 142, 'Lang3', 0, e, 0, e, e, e], [1, 143, 'Lang4', 0, e, 0, e, e, e], [1, 144, 'Lang5', 0, e, 0, e, e, e], [1, 145, 'Abort', 0, e, 0, e, e, e], [1, 146, 'Props', 0, e, 0, e, e, e], [1, 147, 'NumpadParenLeft', 0, e, 0, e, e, e], [1, 148, 'NumpadParenRight', 0, e, 0, e, e, e], [1, 149, 'NumpadBackspace', 0, e, 0, e, e, e], [1, 150, 'NumpadMemoryStore', 0, e, 0, e, e, e], [1, 151, 'NumpadMemoryRecall', 0, e, 0, e, e, e], [1, 152, 'NumpadMemoryClear', 0, e, 0, e, e, e], [1, 153, 'NumpadMemoryAdd', 0, e, 0, e, e, e], [1, 154, 'NumpadMemorySubtract', 0, e, 0, e, e, e], [1, 155, 'NumpadClear', 131, 'Clear', 12, 'VK_CLEAR', e, e], [1, 156, 'NumpadClearEntry', 0, e, 0, e, e, e], [1, 0, e, 5, 'Ctrl', 17, 'VK_CONTROL', e, e], [1, 0, e, 4, 'Shift', 16, 'VK_SHIFT', e, e], [1, 0, e, 6, 'Alt', 18, 'VK_MENU', e, e], [1, 0, e, 57, 'Meta', 91, 'VK_COMMAND', e, e], [1, 157, 'ControlLeft', 5, e, 0, 'VK_LCONTROL', e, e], [1, 158, 'ShiftLeft', 4, e, 0, 'VK_LSHIFT', e, e], [1, 159, 'AltLeft', 6, e, 0, 'VK_LMENU', e, e], [1, 160, 'MetaLeft', 57, e, 0, 'VK_LWIN', e, e], [1, 161, 'ControlRight', 5, e, 0, 'VK_RCONTROL', e, e], [1, 162, 'ShiftRight', 4, e, 0, 'VK_RSHIFT', e, e], [1, 163, 'AltRight', 6, e, 0, 'VK_RMENU', e, e], [1, 164, 'MetaRight', 57, e, 0, 'VK_RWIN', e, e], [1, 165, 'BrightnessUp', 0, e, 0, e, e, e], [1, 166, 'BrightnessDown', 0, e, 0, e, e, e], [1, 167, 'MediaPlay', 0, e, 0, e, e, e], [1, 168, 'MediaRecord', 0, e, 0, e, e, e], [1, 169, 'MediaFastForward', 0, e, 0, e, e, e], [1, 170, 'MediaRewind', 0, e, 0, e, e, e], [1, 171, 'MediaTrackNext', 124, 'MediaTrackNext', 176, 'VK_MEDIA_NEXT_TRACK', e, e], [1, 172, 'MediaTrackPrevious', 125, 'MediaTrackPrevious', 177, 'VK_MEDIA_PREV_TRACK', e, e], [1, 173, 'MediaStop', 126, 'MediaStop', 178, 'VK_MEDIA_STOP', e, e], [1, 174, 'Eject', 0, e, 0, e, e, e], [1, 175, 'MediaPlayPause', 127, 'MediaPlayPause', 179, 'VK_MEDIA_PLAY_PAUSE', e, e], [1, 176, 'MediaSelect', 128, 'LaunchMediaPlayer', 181, 'VK_MEDIA_LAUNCH_MEDIA_SELECT', e, e], [1, 177, 'LaunchMail', 129, 'LaunchMail', 180, 'VK_MEDIA_LAUNCH_MAIL', e, e], [1, 178, 'LaunchApp2', 130, 'LaunchApp2', 183, 'VK_MEDIA_LAUNCH_APP2', e, e], [1, 179, 'LaunchApp1', 0, e, 0, 'VK_MEDIA_LAUNCH_APP1', e, e], [1, 180, 'SelectTask', 0, e, 0, e, e, e], [1, 181, 'LaunchScreenSaver', 0, e, 0, e, e, e], [1, 182, 'BrowserSearch', 120, 'BrowserSearch', 170, 'VK_BROWSER_SEARCH', e, e], [1, 183, 'BrowserHome', 121, 'BrowserHome', 172, 'VK_BROWSER_HOME', e, e], [1, 184, 'BrowserBack', 122, 'BrowserBack', 166, 'VK_BROWSER_BACK', e, e], [1, 185, 'BrowserForward', 123, 'BrowserForward', 167, 'VK_BROWSER_FORWARD', e, e], [1, 186, 'BrowserStop', 0, e, 0, 'VK_BROWSER_STOP', e, e], [1, 187, 'BrowserRefresh', 0, e, 0, 'VK_BROWSER_REFRESH', e, e], [1, 188, 'BrowserFavorites', 0, e, 0, 'VK_BROWSER_FAVORITES', e, e], [1, 189, 'ZoomToggle', 0, e, 0, e, e, e], [1, 190, 'MailReply', 0, e, 0, e, e, e], [1, 191, 'MailForward', 0, e, 0, e, e, e], [1, 192, 'MailSend', 0, e, 0, e, e, e], [1, 0, e, 114, 'KeyInComposition', 229, e, e, e], [1, 0, e, 116, 'ABNT_C2', 194, 'VK_ABNT_C2', e, e], [1, 0, e, 96, 'OEM_8', 223, 'VK_OEM_8', e, e], [1, 0, e, 0, e, 0, 'VK_KANA', e, e], [1, 0, e, 0, e, 0, 'VK_HANGUL', e, e], [1, 0, e, 0, e, 0, 'VK_JUNJA', e, e], [1, 0, e, 0, e, 0, 'VK_FINAL', e, e], [1, 0, e, 0, e, 0, 'VK_HANJA', e, e], [1, 0, e, 0, e, 0, 'VK_KANJI', e, e], [1, 0, e, 0, e, 0, 'VK_CONVERT', e, e], [1, 0, e, 0, e, 0, 'VK_NONCONVERT', e, e], [1, 0, e, 0, e, 0, 'VK_ACCEPT', e, e], [1, 0, e, 0, e, 0, 'VK_MODECHANGE', e, e], [1, 0, e, 0, e, 0, 'VK_SELECT', e, e], [1, 0, e, 0, e, 0, 'VK_PRINT', e, e], [1, 0, e, 0, e, 0, 'VK_EXECUTE', e, e], [1, 0, e, 0, e, 0, 'VK_SNAPSHOT', e, e], [1, 0, e, 0, e, 0, 'VK_HELP', e, e], [1, 0, e, 0, e, 0, 'VK_APPS', e, e], [1, 0, e, 0, e, 0, 'VK_PROCESSKEY', e, e], [1, 0, e, 0, e, 0, 'VK_PACKET', e, e], [1, 0, e, 0, e, 0, 'VK_DBE_SBCSCHAR', e, e], [1, 0, e, 0, e, 0, 'VK_DBE_DBCSCHAR', e, e], [1, 0, e, 0, e, 0, 'VK_ATTN', e, e], [1, 0, e, 0, e, 0, 'VK_CRSEL', e, e], [1, 0, e, 0, e, 0, 'VK_EXSEL', e, e], [1, 0, e, 0, e, 0, 'VK_EREOF', e, e], [1, 0, e, 0, e, 0, 'VK_PLAY', e, e], [1, 0, e, 0, e, 0, 'VK_ZOOM', e, e], [1, 0, e, 0, e, 0, 'VK_NONAME', e, e], [1, 0, e, 0, e, 0, 'VK_PA1', e, e], [1, 0, e, 0, e, 0, 'VK_OEM_CLEAR', e, e]],
        l = [],
        u = [];
      for (const b of s) {
        const [f, y, w, E, S, C, r, a, g] = b;
        if (u[y] || (u[y] = !0, _[y] = w, p[w] = y, c[w.toLowerCase()] = y, f && (t.IMMUTABLE_CODE_TO_KEY_CODE[y] = E, E !== 0 && E !== 3 && E !== 5 && E !== 4 && E !== 6 && E !== 57 && (t.IMMUTABLE_KEY_CODE_TO_CODE[E] = y))), !l[E]) {
          if (l[E] = !0, !S) throw new Error(`String representation missing for key code ${E} around scan code ${w}`);
          R.define(E, S), i.define(E, a || S), d.define(E, g || a || S);
        }
        C && (t.EVENT_KEY_CODE_MAP[C] = E), r && (t.NATIVE_WINDOWS_KEY_CODE_TO_KEY_CODE[r] = E);
      }
      t.IMMUTABLE_KEY_CODE_TO_CODE[3] = 46;
    })();
    var o;
    (function(e) {
      function s(w) {
        return R.keyCodeToStr(w);
      }

      e.toString = s;

      function l(w) {
        return R.strToKeyCode(w);
      }

      e.fromString = l;

      function u(w) {
        return i.keyCodeToStr(w);
      }

      e.toUserSettingsUS = u;

      function b(w) {
        return d.keyCodeToStr(w);
      }

      e.toUserSettingsGeneral = b;

      function f(w) {
        return i.strToKeyCode(w) || d.strToKeyCode(w);
      }

      e.fromUserSettings = f;

      function y(w) {
        if (w >= 98 && w <= 113) return null;
        switch (w) {
          case 16:
            return 'Up';
          case 18:
            return 'Down';
          case 15:
            return 'Left';
          case 17:
            return 'Right';
        }
        return R.keyCodeToStr(w);
      }

      e.toElectronAccelerator = y;
    })(o || (t.KeyCodeUtils = o = {}));

    function L(e, s) {
      const l = (s & 65535) << 16 >>> 0;
      return (e | l) >>> 0;
    }

    t.KeyChord = L;
  }), X(J[36], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Lazy = void 0;

    class M {
      constructor(i) {
        this.executor = i, this._didRun = !1;
      }

      get value() {
        if (!this._didRun) {
          try {
            this._value = this.executor();
          } catch (i) {
            this._error = i;
          } finally {
            this._didRun = !0;
          }
        }
        if (this._error) throw this._error;
        return this._value;
      }

      get rawValue() {
        return this._value;
      }
    }

    t.Lazy = M;
  }), X(J[13], Z([0, 1, 20, 21]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.DisposableMap = t.ImmortalReference = t.RefCountedDisposable = t.MutableDisposable = t.Disposable = t.DisposableStore = t.toDisposable = t.combinedDisposable = t.dispose = t.isDisposable = t.markAsSingleton = t.markAsDisposed = t.trackDisposable = t.setDisposableTracker = void 0;
    const i = !1;
    let d = null;

    function _(r) {
      d = r;
    }

    if (t.setDisposableTracker = _, i) {
      const r = '__is_disposable_tracked__';
      _(new class {
        trackDisposable(a) {
          const g = new Error('Potentially leaked disposable').stack;
          setTimeout(() => {
            a[r] || console.log(g);
          }, 3e3);
        }

        setParent(a, g) {
          if (a && a !== y.None) {
            try {
              a[r] = !0;
            } catch {
            }
          }
        }

        markAsDisposed(a) {
          if (a && a !== y.None) {
            try {
              a[r] = !0;
            } catch {
            }
          }
        }

        markAsSingleton(a) {
        }
      });
    }

    function p(r) {
      return d?.trackDisposable(r), r;
    }

    t.trackDisposable = p;

    function c(r) {
      d?.markAsDisposed(r);
    }

    t.markAsDisposed = c;

    function o(r, a) {
      d?.setParent(r, a);
    }

    function L(r, a) {
      if (d) for (const g of r) d.setParent(g, a);
    }

    function e(r) {
      return d?.markAsSingleton(r), r;
    }

    t.markAsSingleton = e;

    function s(r) {
      return typeof r.dispose == 'function' && r.dispose.length === 0;
    }

    t.isDisposable = s;

    function l(r) {
      if (R.Iterable.is(r)) {
        const a = [];
        for (const g of r) {
          if (g) {
            try {
              g.dispose();
            } catch (m) {
              a.push(m);
            }
          }
        }
        if (a.length === 1) throw a[0];
        if (a.length > 1) throw new AggregateError(a, 'Encountered errors while disposing of store');
        return Array.isArray(r) ? [] : r;
      } else if (r) return r.dispose(), r;
    }

    t.dispose = l;

    function u(...r) {
      const a = b(() => l(r));
      return L(r, a), a;
    }

    t.combinedDisposable = u;

    function b(r) {
      const a = p({
        dispose: (0, M.createSingleCallFunction)(() => {
          c(a), r();
        }),
      });
      return a;
    }

    t.toDisposable = b;

    class f {
      constructor() {
        this._toDispose = new Set, this._isDisposed = !1, p(this);
      }

      get isDisposed() {
        return this._isDisposed;
      }

      dispose() {
        this._isDisposed || (c(this), this._isDisposed = !0, this.clear());
      }

      clear() {
        if (this._toDispose.size !== 0) {
          try {
            l(this._toDispose);
          } finally {
            this._toDispose.clear();
          }
        }
      }

      add(a) {
        if (!a) return a;
        if (a === this) throw new Error('Cannot register a disposable on itself!');
        return o(a, this), this._isDisposed ? f.DISABLE_DISPOSED_WARNING || console.warn(new Error('Trying to add a disposable to a DisposableStore that has already been disposed of. The added object will be leaked!').stack) : this._toDispose.add(a), a;
      }

      deleteAndLeak(a) {
        a && this._toDispose.has(a) && (this._toDispose.delete(a), o(a, null));
      }
    }

    t.DisposableStore = f, f.DISABLE_DISPOSED_WARNING = !1;

    class y {
      constructor() {
        this._store = new f, p(this), o(this._store, this);
      }

      dispose() {
        c(this), this._store.dispose();
      }

      _register(a) {
        if (a === this) throw new Error('Cannot register a disposable on itself!');
        return this._store.add(a);
      }
    }

    t.Disposable = y, y.None = Object.freeze({
      dispose() {
      },
    });

    class w {
      constructor() {
        this._isDisposed = !1, p(this);
      }

      get value() {
        return this._isDisposed ? void 0 : this._value;
      }

      set value(a) {
        var g;
        this._isDisposed || a === this._value || ((g = this._value) === null || g === void 0 || g.dispose(), a && o(a, this), this._value = a);
      }

      clear() {
        this.value = void 0;
      }

      dispose() {
        var a;
        this._isDisposed = !0, c(this), (a = this._value) === null || a === void 0 || a.dispose(), this._value = void 0;
      }
    }

    t.MutableDisposable = w;

    class E {
      constructor(a) {
        this._disposable = a, this._counter = 1;
      }

      acquire() {
        return this._counter++, this;
      }

      release() {
        return --this._counter === 0 && this._disposable.dispose(), this;
      }
    }

    t.RefCountedDisposable = E;

    class S {
      constructor(a) {
        this.object = a;
      }

      dispose() {
      }
    }

    t.ImmortalReference = S;

    class C {
      constructor() {
        this._store = new Map, this._isDisposed = !1, p(this);
      }

      dispose() {
        c(this), this._isDisposed = !0, this.clearAndDisposeAll();
      }

      clearAndDisposeAll() {
        if (this._store.size) {
          try {
            l(this._store.values());
          } finally {
            this._store.clear();
          }
        }
      }

      get(a) {
        return this._store.get(a);
      }

      set(a, g, m = !1) {
        var h;
        this._isDisposed && console.warn(new Error('Trying to add a disposable to a DisposableMap that has already been disposed of. The added object will be leaked!').stack), m || (h = this._store.get(a)) === null || h === void 0 || h.dispose(), this._store.set(a, g);
      }

      deleteAndDispose(a) {
        var g;
        (g = this._store.get(a)) === null || g === void 0 || g.dispose(), this._store.delete(a);
      }

      [Symbol.iterator]() {
        return this._store[Symbol.iterator]();
      }
    }

    t.DisposableMap = C;
  }), X(J[22], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LinkedList = void 0;

    class M {
      constructor(d) {
        this.element = d, this.next = M.Undefined, this.prev = M.Undefined;
      }
    }

    M.Undefined = new M(void 0);

    class R {
      constructor() {
        this._first = M.Undefined, this._last = M.Undefined, this._size = 0;
      }

      get size() {
        return this._size;
      }

      isEmpty() {
        return this._first === M.Undefined;
      }

      clear() {
        let d = this._first;
        for (; d !== M.Undefined;) {
          const _ = d.next;
          d.prev = M.Undefined, d.next = M.Undefined, d = _;
        }
        this._first = M.Undefined, this._last = M.Undefined, this._size = 0;
      }

      unshift(d) {
        return this._insert(d, !1);
      }

      push(d) {
        return this._insert(d, !0);
      }

      _insert(d, _) {
        const p = new M(d);
        if (this._first === M.Undefined) {
          this._first = p, this._last = p;
        } else if (_) {
          const o = this._last;
          this._last = p, p.prev = o, o.next = p;
        } else {
          const o = this._first;
          this._first = p, p.next = o, o.prev = p;
        }
        this._size += 1;
        let c = !1;
        return () => {
          c || (c = !0, this._remove(p));
        };
      }

      shift() {
        if (this._first !== M.Undefined) {
          const d = this._first.element;
          return this._remove(this._first), d;
        }
      }

      pop() {
        if (this._last !== M.Undefined) {
          const d = this._last.element;
          return this._remove(this._last), d;
        }
      }

      _remove(d) {
        if (d.prev !== M.Undefined && d.next !== M.Undefined) {
          const _ = d.prev;
          _.next = d.next, d.next.prev = _;
        } else {
          d.prev === M.Undefined && d.next === M.Undefined ? (this._first = M.Undefined, this._last = M.Undefined) : d.next === M.Undefined ? (this._last = this._last.prev, this._last.next = M.Undefined) : d.prev === M.Undefined && (this._first = this._first.next, this._first.prev = M.Undefined);
        }
        this._size -= 1;
      }

      * [Symbol.iterator]() {
        let d = this._first;
        for (; d !== M.Undefined;) yield d.element, d = d.next;
      }
    }

    t.LinkedList = R;
  }), X(J[37], Z([0, 1]), function(q, t) {
    'use strict';
    var M,
      R;
    Object.defineProperty(t, '__esModule', { value: !0 }), t.SetMap = t.BidirectionalMap = t.LRUCache = t.LinkedMap = t.ResourceMap = void 0;

    class i {
      constructor(s, l) {
        this.uri = s, this.value = l;
      }
    }

    function d(e) {
      return Array.isArray(e);
    }

    class _ {
      constructor(s, l) {
        if (this[M] = 'ResourceMap', s instanceof _) {
          this.map = new Map(s.map), this.toKey = l ?? _.defaultToKey;
        } else if (d(s)) {
          this.map = new Map, this.toKey = l ?? _.defaultToKey;
          for (const [u, b] of s) this.set(u, b);
        } else {
          this.map = new Map, this.toKey = s ?? _.defaultToKey;
        }
      }

      get size() {
        return this.map.size;
      }

      set(s, l) {
        return this.map.set(this.toKey(s), new i(s, l)), this;
      }

      get(s) {
        var l;
        return (l = this.map.get(this.toKey(s))) === null || l === void 0 ? void 0 : l.value;
      }

      has(s) {
        return this.map.has(this.toKey(s));
      }

      clear() {
        this.map.clear();
      }

      delete(s) {
        return this.map.delete(this.toKey(s));
      }

      forEach(s, l) {
        typeof l < 'u' && (s = s.bind(l));
        for (const [u, b] of this.map) s(b.value, b.uri, this);
      }

      * values() {
        for (const s of this.map.values()) yield s.value;
      }

      * keys() {
        for (const s of this.map.values()) yield s.uri;
      }

      * entries() {
        for (const s of this.map.values()) yield[s.uri, s.value];
      }

      * [(M = Symbol.toStringTag, Symbol.iterator)]() {
        for (const [, s] of this.map) yield[s.uri, s.value];
      }
    }

    t.ResourceMap = _, _.defaultToKey = e => e.toString();

    class p {
      constructor() {
        this[R] = 'LinkedMap', this._map = new Map, this._head = void 0, this._tail = void 0, this._size = 0, this._state = 0;
      }

      get size() {
        return this._size;
      }

      get first() {
        var s;
        return (s = this._head) === null || s === void 0 ? void 0 : s.value;
      }

      get last() {
        var s;
        return (s = this._tail) === null || s === void 0 ? void 0 : s.value;
      }

      clear() {
        this._map.clear(), this._head = void 0, this._tail = void 0, this._size = 0, this._state++;
      }

      isEmpty() {
        return !this._head && !this._tail;
      }

      has(s) {
        return this._map.has(s);
      }

      get(s, l = 0) {
        const u = this._map.get(s);
        if (u) return l !== 0 && this.touch(u, l), u.value;
      }

      set(s, l, u = 0) {
        let b = this._map.get(s);
        if (b) {
          b.value = l, u !== 0 && this.touch(b, u);
        } else {
          switch (b = {
            key: s,
            value: l,
            next: void 0,
            previous: void 0,
          }, u) {
            case 0:
              this.addItemLast(b);
              break;
            case 1:
              this.addItemFirst(b);
              break;
            case 2:
              this.addItemLast(b);
              break;
            default:
              this.addItemLast(b);
              break;
          }
          this._map.set(s, b), this._size++;
        }
        return this;
      }

      delete(s) {
        return !!this.remove(s);
      }

      remove(s) {
        const l = this._map.get(s);
        if (l) return this._map.delete(s), this.removeItem(l), this._size--, l.value;
      }

      shift() {
        if (!this._head && !this._tail) return;
        if (!this._head || !this._tail) throw new Error('Invalid list');
        const s = this._head;
        return this._map.delete(s.key), this.removeItem(s), this._size--, s.value;
      }

      forEach(s, l) {
        const u = this._state;
        let b = this._head;
        for (; b;) {
          if (l ? s.bind(l)(b.value, b.key, this) : s(b.value, b.key, this), this._state !== u) throw new Error('LinkedMap got modified during iteration.');
          b = b.next;
        }
      }

      keys() {
        const s = this,
          l = this._state;
        let u = this._head;
        const b = {
          [Symbol.iterator]() {
            return b;
          },
          next() {
            if (s._state !== l) throw new Error('LinkedMap got modified during iteration.');
            if (u) {
              const f = {
                value: u.key,
                done: !1,
              };
              return u = u.next, f;
            } else {
              return {
                value: void 0,
                done: !0,
              };
            }
          },
        };
        return b;
      }

      values() {
        const s = this,
          l = this._state;
        let u = this._head;
        const b = {
          [Symbol.iterator]() {
            return b;
          },
          next() {
            if (s._state !== l) throw new Error('LinkedMap got modified during iteration.');
            if (u) {
              const f = {
                value: u.value,
                done: !1,
              };
              return u = u.next, f;
            } else {
              return {
                value: void 0,
                done: !0,
              };
            }
          },
        };
        return b;
      }

      entries() {
        const s = this,
          l = this._state;
        let u = this._head;
        const b = {
          [Symbol.iterator]() {
            return b;
          },
          next() {
            if (s._state !== l) throw new Error('LinkedMap got modified during iteration.');
            if (u) {
              const f = {
                value: [u.key, u.value],
                done: !1,
              };
              return u = u.next, f;
            } else {
              return {
                value: void 0,
                done: !0,
              };
            }
          },
        };
        return b;
      }

      [(R = Symbol.toStringTag, Symbol.iterator)]() {
        return this.entries();
      }

      trimOld(s) {
        if (s >= this.size) return;
        if (s === 0) {
          this.clear();
          return;
        }
        let l = this._head,
          u = this.size;
        for (; l && u > s;) this._map.delete(l.key), l = l.next, u--;
        this._head = l, this._size = u, l && (l.previous = void 0), this._state++;
      }

      addItemFirst(s) {
        if (!this._head && !this._tail) this._tail = s; else if (this._head) s.next = this._head, this._head.previous = s; else throw new Error('Invalid list');
        this._head = s, this._state++;
      }

      addItemLast(s) {
        if (!this._head && !this._tail) this._head = s; else if (this._tail) s.previous = this._tail, this._tail.next = s; else throw new Error('Invalid list');
        this._tail = s, this._state++;
      }

      removeItem(s) {
        if (s === this._head && s === this._tail) {
          this._head = void 0, this._tail = void 0;
        } else if (s === this._head) {
          if (!s.next) throw new Error('Invalid list');
          s.next.previous = void 0, this._head = s.next;
        } else if (s === this._tail) {
          if (!s.previous) throw new Error('Invalid list');
          s.previous.next = void 0, this._tail = s.previous;
        } else {
          const l = s.next,
            u = s.previous;
          if (!l || !u) throw new Error('Invalid list');
          l.previous = u, u.next = l;
        }
        s.next = void 0, s.previous = void 0, this._state++;
      }

      touch(s, l) {
        if (!this._head || !this._tail) throw new Error('Invalid list');
        if (!(l !== 1 && l !== 2)) {
          if (l === 1) {
            if (s === this._head) return;
            const u = s.next,
              b = s.previous;
            s === this._tail ? (b.next = void 0, this._tail = b) : (u.previous = b, b.next = u), s.previous = void 0, s.next = this._head, this._head.previous = s, this._head = s, this._state++;
          } else if (l === 2) {
            if (s === this._tail) return;
            const u = s.next,
              b = s.previous;
            s === this._head ? (u.previous = void 0, this._head = u) : (u.previous = b, b.next = u), s.next = void 0, s.previous = this._tail, this._tail.next = s, this._tail = s, this._state++;
          }
        }
      }

      toJSON() {
        const s = [];
        return this.forEach((l, u) => {
          s.push([u, l]);
        }), s;
      }

      fromJSON(s) {
        this.clear();
        for (const [l, u] of s) this.set(l, u);
      }
    }

    t.LinkedMap = p;

    class c extends p {
      constructor(s, l = 1) {
        super(), this._limit = s, this._ratio = Math.min(Math.max(0, l), 1);
      }

      get limit() {
        return this._limit;
      }

      set limit(s) {
        this._limit = s, this.checkTrim();
      }

      get(s, l = 2) {
        return super.get(s, l);
      }

      peek(s) {
        return super.get(s, 0);
      }

      set(s, l) {
        return super.set(s, l, 2), this.checkTrim(), this;
      }

      checkTrim() {
        this.size > this._limit && this.trimOld(Math.round(this._limit * this._ratio));
      }
    }

    t.LRUCache = c;

    class o {
      constructor(s) {
        if (this._m1 = new Map, this._m2 = new Map, s) for (const [l, u] of s) this.set(l, u);
      }

      clear() {
        this._m1.clear(), this._m2.clear();
      }

      set(s, l) {
        this._m1.set(s, l), this._m2.set(l, s);
      }

      get(s) {
        return this._m1.get(s);
      }

      getKey(s) {
        return this._m2.get(s);
      }

      delete(s) {
        const l = this._m1.get(s);
        return l === void 0 ? !1 : (this._m1.delete(s), this._m2.delete(l), !0);
      }

      keys() {
        return this._m1.keys();
      }

      values() {
        return this._m1.values();
      }
    }

    t.BidirectionalMap = o;

    class L {
      constructor() {
        this.map = new Map;
      }

      add(s, l) {
        let u = this.map.get(s);
        u || (u = new Set, this.map.set(s, u)), u.add(l);
      }

      delete(s, l) {
        const u = this.map.get(s);
        u && (u.delete(l), u.size === 0 && this.map.delete(s));
      }

      forEach(s, l) {
        const u = this.map.get(s);
        u && u.forEach(l);
      }

      get(s) {
        const l = this.map.get(s);
        return l || new Set;
      }
    }

    t.SetMap = L;
  }), X(J[23], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.StopWatch = void 0;
    const M = globalThis.performance && typeof globalThis.performance.now == 'function';

    class R {
      constructor(d) {
        this._now = M && d === !1 ? Date.now : globalThis.performance.now.bind(globalThis.performance), this._startTime = this._now(), this._stopTime = -1;
      }

      static create(d) {
        return new R(d);
      }

      stop() {
        this._stopTime = this._now();
      }

      elapsed() {
        return this._stopTime !== -1 ? this._stopTime - this._startTime : this._now() - this._startTime;
      }
    }

    t.StopWatch = R;
  }), X(J[9], Z([0, 1, 5, 20, 13, 22, 23]), function(q, t, M, R, i, d, _) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Relay = t.EventBufferer = t.EventMultiplexer = t.MicrotaskEmitter = t.DebounceEmitter = t.PauseableEmitter = t.createEventDeliveryQueue = t.Emitter = t.EventProfiling = t.Event = void 0;
    const p = !1,
      c = !1;
    var o;
    (function(h) {
      h.None = () => i.Disposable.None;

      function v(K) {
        if (c) {
          const { onDidAddListener: j } = K,
            Q = l.create();
          let Y = 0;
          K.onDidAddListener = () => {
            ++Y === 2 && (console.warn('snapshotted emitter LIKELY used public and SHOULD HAVE BEEN created with DisposableStore. snapshotted here'), Q.print()), j?.();
          };
        }
      }

      function N(K, j) {
        return F(K, () => {
        }, 0, void 0, !0, void 0, j);
      }

      h.defer = N;

      function A(K) {
        return (j, Q = null, Y) => {
          let te = !1,
            ie;
          return ie = K(re => {
            if (!te) return ie ? ie.dispose() : te = !0, j.call(Q, re);
          }, null, Y), te && ie.dispose(), ie;
        };
      }

      h.once = A;

      function D(K, j, Q) {
        return x((Y, te = null, ie) => K(re => Y.call(te, j(re)), null, ie), Q);
      }

      h.map = D;

      function P(K, j, Q) {
        return x((Y, te = null, ie) => K(re => {
          j(re), Y.call(te, re);
        }, null, ie), Q);
      }

      h.forEach = P;

      function T(K, j, Q) {
        return x((Y, te = null, ie) => K(re => j(re) && Y.call(te, re), null, ie), Q);
      }

      h.filter = T;

      function I(K) {
        return K;
      }

      h.signal = I;

      function B(...K) {
        return (j, Q = null, Y) => {
          const te = (0, i.combinedDisposable)(...K.map(ie => ie(re => j.call(Q, re))));
          return O(te, Y);
        };
      }

      h.any = B;

      function z(K, j, Q, Y) {
        let te = Q;
        return D(K, ie => (te = j(te, ie), te), Y);
      }

      h.reduce = z;

      function x(K, j) {
        let Q;
        const Y = {
          onWillAddFirstListener() {
            Q = K(te.fire, te);
          },
          onDidRemoveLastListener() {
            Q?.dispose();
          },
        };
        j || v(Y);
        const te = new y(Y);
        return j?.add(te), te.event;
      }

      function O(K, j) {
        return j instanceof Array ? j.push(K) : j && j.add(K), K;
      }

      function F(K, j, Q = 100, Y = !1, te = !1, ie, re) {
        let oe,
          le,
          k,
          U = 0,
          V;
        const $ = {
          leakWarningThreshold: ie,
          onWillAddFirstListener() {
            oe = K(ae => {
              U++, le = j(le, ae), Y && !k && (ee.fire(le), le = void 0), V = () => {
                const he = le;
                le = void 0, k = void 0, (!Y || U > 1) && ee.fire(he), U = 0;
              }, typeof Q == 'number' ? (clearTimeout(k), k = setTimeout(V, Q)) : k === void 0 && (k = 0, queueMicrotask(V));
            });
          },
          onWillRemoveListener() {
            te && U > 0 && V?.();
          },
          onDidRemoveLastListener() {
            V = void 0, oe.dispose();
          },
        };
        re || v($);
        const ee = new y($);
        return re?.add(ee), ee.event;
      }

      h.debounce = F;

      function W(K, j = 0, Q) {
        return h.debounce(K, (Y, te) => Y ? (Y.push(te), Y) : [te], j, void 0, !0, void 0, Q);
      }

      h.accumulate = W;

      function H(K, j = (Y, te) => Y === te, Q) {
        let Y = !0,
          te;
        return T(K, ie => {
          const re = Y || !j(ie, te);
          return Y = !1, te = ie, re;
        }, Q);
      }

      h.latch = H;

      function G(K, j, Q) {
        return [h.filter(K, j, Q), h.filter(K, Y => !j(Y), Q)];
      }

      h.split = G;

      function ne(K, j = !1, Q = [], Y) {
        let te = Q.slice(),
          ie = K(le => {
            te ? te.push(le) : oe.fire(le);
          });
        Y && Y.add(ie);
        const re = () => {
            te?.forEach(le => oe.fire(le)), te = null;
          },
          oe = new y({
            onWillAddFirstListener() {
              ie || (ie = K(le => oe.fire(le)), Y && Y.add(ie));
            },
            onDidAddFirstListener() {
              te && (j ? setTimeout(re) : re());
            },
            onDidRemoveLastListener() {
              ie && ie.dispose(), ie = null;
            },
          });
        return Y && Y.add(oe), oe.event;
      }

      h.buffer = ne;

      function se(K, j) {
        return (Y, te, ie) => {
          const re = j(new be);
          return K(function(oe) {
            const le = re.evaluate(oe);
            le !== n && Y.call(te, le);
          }, void 0, ie);
        };
      }

      h.chain = se;
      const n = Symbol('HaltChainable');

      class be {
        constructor() {
          this.steps = [];
        }

        map(j) {
          return this.steps.push(j), this;
        }

        forEach(j) {
          return this.steps.push(Q => (j(Q), Q)), this;
        }

        filter(j) {
          return this.steps.push(Q => j(Q) ? Q : n), this;
        }

        reduce(j, Q) {
          let Y = Q;
          return this.steps.push(te => (Y = j(Y, te), Y)), this;
        }

        latch(j = (Q, Y) => Q === Y) {
          let Q = !0,
            Y;
          return this.steps.push(te => {
            const ie = Q || !j(te, Y);
            return Q = !1, Y = te, ie ? te : n;
          }), this;
        }

        evaluate(j) {
          for (const Q of this.steps) if (j = Q(j), j === n) break;
          return j;
        }
      }

      function pe(K, j, Q = Y => Y) {
        const Y = (...oe) => re.fire(Q(...oe)),
          te = () => K.on(j, Y),
          ie = () => K.removeListener(j, Y),
          re = new y({
            onWillAddFirstListener: te,
            onDidRemoveLastListener: ie,
          });
        return re.event;
      }

      h.fromNodeEventEmitter = pe;

      function Le(K, j, Q = Y => Y) {
        const Y = (...oe) => re.fire(Q(...oe)),
          te = () => K.addEventListener(j, Y),
          ie = () => K.removeEventListener(j, Y),
          re = new y({
            onWillAddFirstListener: te,
            onDidRemoveLastListener: ie,
          });
        return re.event;
      }

      h.fromDOMEventEmitter = Le;

      function we(K) {
        return new Promise(j => A(K)(j));
      }

      h.toPromise = we;

      function Ce(K) {
        const j = new y;
        return K.then(Q => {
          j.fire(Q);
        }, () => {
          j.fire(void 0);
        })
          .finally(() => {
            j.dispose();
          }), j.event;
      }

      h.fromPromise = Ce;

      function Se(K, j, Q) {
        return j(Q), K(Y => j(Y));
      }

      h.runAndSubscribe = Se;

      class _e {
        constructor(j, Q) {
          this._observable = j, this._counter = 0, this._hasChanged = !1;
          const Y = {
            onWillAddFirstListener: () => {
              j.addObserver(this);
            },
            onDidRemoveLastListener: () => {
              j.removeObserver(this);
            },
          };
          Q || v(Y), this.emitter = new y(Y), Q && Q.add(this.emitter);
        }

        beginUpdate(j) {
          this._counter++;
        }

        handlePossibleChange(j) {
        }

        handleChange(j, Q) {
          this._hasChanged = !0;
        }

        endUpdate(j) {
          this._counter--, this._counter === 0 && (this._observable.reportChanges(), this._hasChanged && (this._hasChanged = !1, this.emitter.fire(this._observable.get())));
        }
      }

      function ye(K, j) {
        return new _e(K, j).emitter.event;
      }

      h.fromObservable = ye;

      function Ee(K) {
        return (j, Q, Y) => {
          let te = 0,
            ie = !1;
          const re = {
            beginUpdate() {
              te++;
            },
            endUpdate() {
              te--, te === 0 && (K.reportChanges(), ie && (ie = !1, j.call(Q)));
            },
            handlePossibleChange() {
            },
            handleChange() {
              ie = !0;
            },
          };
          K.addObserver(re), K.reportChanges();
          const oe = {
            dispose() {
              K.removeObserver(re);
            },
          };
          return Y instanceof i.DisposableStore ? Y.add(oe) : Array.isArray(Y) && Y.push(oe), oe;
        };
      }

      h.fromObservableLight = Ee;
    })(o || (t.Event = o = {}));

    class L {
      constructor(v) {
        this.listenerCount = 0, this.invocationCount = 0, this.elapsedOverall = 0, this.durations = [], this.name = `${v}_${L._idPool++}`, L.all.add(this);
      }

      start(v) {
        this._stopWatch = new _.StopWatch, this.listenerCount = v;
      }

      stop() {
        if (this._stopWatch) {
          const v = this._stopWatch.elapsed();
          this.durations.push(v), this.elapsedOverall += v, this.invocationCount += 1, this._stopWatch = void 0;
        }
      }
    }

    t.EventProfiling = L, L.all = new Set, L._idPool = 0;
    let e = -1;

    class s {
      constructor(v, N = Math.random()
        .toString(18)
        .slice(2, 5)) {
        this.threshold = v, this.name = N, this._warnCountdown = 0;
      }

      dispose() {
        var v;
        (v = this._stacks) === null || v === void 0 || v.clear();
      }

      check(v, N) {
        const A = this.threshold;
        if (A <= 0 || N < A) return;
        this._stacks || (this._stacks = new Map);
        const D = this._stacks.get(v.value) || 0;
        if (this._stacks.set(v.value, D + 1), this._warnCountdown -= 1, this._warnCountdown <= 0) {
          this._warnCountdown = A * .5;
          let P,
            T = 0;
          for (const [I, B] of this._stacks) (!P || T < B) && (P = I, T = B);
          console.warn(`[${this.name}] potential listener LEAK detected, having ${N} listeners already. MOST frequent listener (${T}):`), console.warn(P);
        }
        return () => {
          const P = this._stacks.get(v.value) || 0;
          this._stacks.set(v.value, P - 1);
        };
      }
    }

    class l {
      constructor(v) {
        this.value = v;
      }

      static create() {
        var v;
        return new l((v = new Error().stack) !== null && v !== void 0 ? v : '');
      }

      print() {
        console.warn(this.value.split(`
`)
          .slice(2)
          .join(`
`));
      }
    }

    class u {
      constructor(v) {
        this.value = v;
      }
    }

    const b = 2,
      f = (h, v) => {
        if (h instanceof u) {
          v(h);
        } else {
          for (let N = 0; N < h.length; N++) {
            const A = h[N];
            A && v(A);
          }
        }
      };

    class y {
      constructor(v) {
        var N,
          A,
          D,
          P,
          T;
        this._size = 0, this._options = v, this._leakageMon = e > 0 || !((N = this._options) === null || N === void 0) && N.leakWarningThreshold ? new s((D = (A = this._options) === null || A === void 0 ? void 0 : A.leakWarningThreshold) !== null && D !== void 0 ? D : e) : void 0, this._perfMon = !((P = this._options) === null || P === void 0) && P._profName ? new L(this._options._profName) : void 0, this._deliveryQueue = (T = this._options) === null || T === void 0 ? void 0 : T.deliveryQueue;
      }

      get event() {
        var v;
        return (v = this._event) !== null && v !== void 0 || (this._event = (N, A, D) => {
          var P,
            T,
            I,
            B,
            z;
          if (this._leakageMon && this._size > this._leakageMon.threshold * 3) return console.warn(`[${this._leakageMon.name}] REFUSES to accept new listeners because it exceeded its threshold by far`), i.Disposable.None;
          if (this._disposed) return i.Disposable.None;
          A && (N = N.bind(A));
          const x = new u(N);
          let O,
            F;
          this._leakageMon && this._size >= Math.ceil(this._leakageMon.threshold * .2) && (x.stack = l.create(), O = this._leakageMon.check(x.stack, this._size + 1)), p && (x.stack = F ?? l.create()), this._listeners ? this._listeners instanceof u ? ((z = this._deliveryQueue) !== null && z !== void 0 || (this._deliveryQueue = new E), this._listeners = [this._listeners, x]) : this._listeners.push(x) : ((T = (P = this._options) === null || P === void 0 ? void 0 : P.onWillAddFirstListener) === null || T === void 0 || T.call(P, this), this._listeners = x, (B = (I = this._options) === null || I === void 0 ? void 0 : I.onDidAddFirstListener) === null || B === void 0 || B.call(I, this)), this._size++;
          const W = (0, i.toDisposable)(() => {
            O?.(), this._removeListener(x);
          });
          return D instanceof i.DisposableStore ? D.add(W) : Array.isArray(D) && D.push(W), W;
        }), this._event;
      }

      dispose() {
        var v,
          N,
          A,
          D;
        if (!this._disposed) {
          if (this._disposed = !0, ((v = this._deliveryQueue) === null || v === void 0 ? void 0 : v.current) === this && this._deliveryQueue.reset(), this._listeners) {
            if (p) {
              const P = this._listeners;
              queueMicrotask(() => {
                f(P, T => {
                  var I;
                  return (I = T.stack) === null || I === void 0 ? void 0 : I.print();
                });
              });
            }
            this._listeners = void 0, this._size = 0;
          }
          (A = (N = this._options) === null || N === void 0 ? void 0 : N.onDidRemoveLastListener) === null || A === void 0 || A.call(N), (D = this._leakageMon) === null || D === void 0 || D.dispose();
        }
      }

      _removeListener(v) {
        var N,
          A,
          D,
          P;
        if ((A = (N = this._options) === null || N === void 0 ? void 0 : N.onWillRemoveListener) === null || A === void 0 || A.call(N, this), !this._listeners) return;
        if (this._size === 1) {
          this._listeners = void 0, (P = (D = this._options) === null || D === void 0 ? void 0 : D.onDidRemoveLastListener) === null || P === void 0 || P.call(D, this), this._size = 0;
          return;
        }
        const T = this._listeners,
          I = T.indexOf(v);
        if (I === -1) throw console.log('disposed?', this._disposed), console.log('size?', this._size), console.log('arr?', JSON.stringify(this._listeners)), new Error('Attempted to dispose unknown listener');
        this._size--, T[I] = void 0;
        const B = this._deliveryQueue.current === this;
        if (this._size * b <= T.length) {
          let z = 0;
          for (let x = 0; x < T.length; x++) T[x] ? T[z++] = T[x] : B && (this._deliveryQueue.end--, z < this._deliveryQueue.i && this._deliveryQueue.i--);
          T.length = z;
        }
      }

      _deliver(v, N) {
        var A;
        if (!v) return;
        const D = ((A = this._options) === null || A === void 0 ? void 0 : A.onListenerError) || M.onUnexpectedError;
        if (!D) {
          v.value(N);
          return;
        }
        try {
          v.value(N);
        } catch (P) {
          D(P);
        }
      }

      _deliverQueue(v) {
        const N = v.current._listeners;
        for (; v.i < v.end;) this._deliver(N[v.i++], v.value);
        v.reset();
      }

      fire(v) {
        var N,
          A,
          D,
          P;
        if (!((N = this._deliveryQueue) === null || N === void 0) && N.current && (this._deliverQueue(this._deliveryQueue), (A = this._perfMon) === null || A === void 0 || A.stop()), (D = this._perfMon) === null || D === void 0 || D.start(this._size), this._listeners) {
          if (this._listeners instanceof u) {
            this._deliver(this._listeners, v);
          } else {
            const T = this._deliveryQueue;
            T.enqueue(this, v, this._listeners.length), this._deliverQueue(T);
          }
        }
        (P = this._perfMon) === null || P === void 0 || P.stop();
      }

      hasListeners() {
        return this._size > 0;
      }
    }

    t.Emitter = y;
    const w = () => new E;
    t.createEventDeliveryQueue = w;

    class E {
      constructor() {
        this.i = -1, this.end = 0;
      }

      enqueue(v, N, A) {
        this.i = 0, this.end = A, this.current = v, this.value = N;
      }

      reset() {
        this.i = this.end, this.current = void 0, this.value = void 0;
      }
    }

    class S extends y {
      constructor(v) {
        super(v), this._isPaused = 0, this._eventQueue = new d.LinkedList, this._mergeFn = v?.merge;
      }

      pause() {
        this._isPaused++;
      }

      resume() {
        if (this._isPaused !== 0 && --this._isPaused === 0) {
          if (this._mergeFn) {
            if (this._eventQueue.size > 0) {
              const v = Array.from(this._eventQueue);
              this._eventQueue.clear(), super.fire(this._mergeFn(v));
            }
          } else {
            for (; !this._isPaused && this._eventQueue.size !== 0;) super.fire(this._eventQueue.shift());
          }
        }
      }

      fire(v) {
        this._size && (this._isPaused !== 0 ? this._eventQueue.push(v) : super.fire(v));
      }
    }

    t.PauseableEmitter = S;

    class C extends S {
      constructor(v) {
        var N;
        super(v), this._delay = (N = v.delay) !== null && N !== void 0 ? N : 100;
      }

      fire(v) {
        this._handle || (this.pause(), this._handle = setTimeout(() => {
          this._handle = void 0, this.resume();
        }, this._delay)), super.fire(v);
      }
    }

    t.DebounceEmitter = C;

    class r extends y {
      constructor(v) {
        super(v), this._queuedEvents = [], this._mergeFn = v?.merge;
      }

      fire(v) {
        this.hasListeners() && (this._queuedEvents.push(v), this._queuedEvents.length === 1 && queueMicrotask(() => {
          this._mergeFn ? super.fire(this._mergeFn(this._queuedEvents)) : this._queuedEvents.forEach(N => super.fire(N)), this._queuedEvents = [];
        }));
      }
    }

    t.MicrotaskEmitter = r;

    class a {
      constructor() {
        this.hasListeners = !1, this.events = [], this.emitter = new y({
          onWillAddFirstListener: () => this.onFirstListenerAdd(),
          onDidRemoveLastListener: () => this.onLastListenerRemove(),
        });
      }

      get event() {
        return this.emitter.event;
      }

      add(v) {
        const N = {
          event: v,
          listener: null,
        };
        this.events.push(N), this.hasListeners && this.hook(N);
        const A = () => {
          this.hasListeners && this.unhook(N);
          const D = this.events.indexOf(N);
          this.events.splice(D, 1);
        };
        return (0, i.toDisposable)((0, R.createSingleCallFunction)(A));
      }

      onFirstListenerAdd() {
        this.hasListeners = !0, this.events.forEach(v => this.hook(v));
      }

      onLastListenerRemove() {
        this.hasListeners = !1, this.events.forEach(v => this.unhook(v));
      }

      hook(v) {
        v.listener = v.event(N => this.emitter.fire(N));
      }

      unhook(v) {
        var N;
        (N = v.listener) === null || N === void 0 || N.dispose(), v.listener = null;
      }

      dispose() {
        var v;
        this.emitter.dispose();
        for (const N of this.events) (v = N.listener) === null || v === void 0 || v.dispose();
        this.events = [];
      }
    }

    t.EventMultiplexer = a;

    class g {
      constructor() {
        this.buffers = [];
      }

      wrapEvent(v) {
        return (N, A, D) => v(P => {
          const T = this.buffers[this.buffers.length - 1];
          T ? T.push(() => N.call(A, P)) : N.call(A, P);
        }, void 0, D);
      }

      bufferEvents(v) {
        const N = [];
        this.buffers.push(N);
        const A = v();
        return this.buffers.pop(), N.forEach(D => D()), A;
      }
    }

    t.EventBufferer = g;

    class m {
      constructor() {
        this.listening = !1, this.inputEvent = o.None, this.inputEventListener = i.Disposable.None, this.emitter = new y({
          onDidAddFirstListener: () => {
            this.listening = !0, this.inputEventListener = this.inputEvent(this.emitter.fire, this.emitter);
          },
          onDidRemoveLastListener: () => {
            this.listening = !1, this.inputEventListener.dispose();
          },
        }), this.event = this.emitter.event;
      }

      set input(v) {
        this.inputEvent = v, this.listening && (this.inputEventListener.dispose(), this.inputEventListener = v(this.emitter.fire, this.emitter));
      }

      dispose() {
        this.inputEventListener.dispose(), this.emitter.dispose();
      }
    }

    t.Relay = m;
  }), X(J[38], Z([0, 1, 9]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.CancellationTokenSource = t.CancellationToken = void 0;
    const R = Object.freeze(function(p, c) {
      const o = setTimeout(p.bind(c), 0);
      return {
        dispose() {
          clearTimeout(o);
        },
      };
    });
    var i;
    (function(p) {
      function c(o) {
        return o === p.None || o === p.Cancelled || o instanceof d ? !0 : !o || typeof o != 'object' ? !1 : typeof o.isCancellationRequested == 'boolean' && typeof o.onCancellationRequested == 'function';
      }

      p.isCancellationToken = c, p.None = Object.freeze({
        isCancellationRequested: !1,
        onCancellationRequested: M.Event.None,
      }), p.Cancelled = Object.freeze({
        isCancellationRequested: !0,
        onCancellationRequested: R,
      });
    })(i || (t.CancellationToken = i = {}));

    class d {
      constructor() {
        this._isCancelled = !1, this._emitter = null;
      }

      get isCancellationRequested() {
        return this._isCancelled;
      }

      get onCancellationRequested() {
        return this._isCancelled ? R : (this._emitter || (this._emitter = new M.Emitter), this._emitter.event);
      }

      cancel() {
        this._isCancelled || (this._isCancelled = !0, this._emitter && (this._emitter.fire(void 0), this.dispose()));
      }

      dispose() {
        this._emitter && (this._emitter.dispose(), this._emitter = null);
      }
    }

    class _ {
      constructor(c) {
        this._token = void 0, this._parentListener = void 0, this._parentListener = c && c.onCancellationRequested(this.cancel, this);
      }

      get token() {
        return this._token || (this._token = new d), this._token;
      }

      cancel() {
        this._token ? this._token instanceof d && this._token.cancel() : this._token = i.Cancelled;
      }

      dispose(c = !1) {
        var o;
        c && this.cancel(), (o = this._parentListener) === null || o === void 0 || o.dispose(), this._token ? this._token instanceof d && this._token.dispose() : this._token = i.None;
      }
    }

    t.CancellationTokenSource = _;
  }), X(J[6], Z([0, 1, 32, 36]), function(q, t, M, R) {
    'use strict';
    var i;
    Object.defineProperty(t, '__esModule', { value: !0 }), t.InvisibleCharacters = t.AmbiguousCharacters = t.noBreakWhitespace = t.getLeftDeleteOffset = t.singleLetterHash = t.containsUppercaseCharacter = t.startsWithUTF8BOM = t.UTF8_BOM_CHARACTER = t.isEmojiImprecise = t.isFullWidthCharacter = t.containsUnusualLineTerminators = t.UNUSUAL_LINE_TERMINATORS = t.isBasicASCII = t.containsRTL = t.getCharContainingOffset = t.prevCharLength = t.nextCharLength = t.GraphemeIterator = t.CodePointIterator = t.getNextCodePoint = t.computeCodePoint = t.isLowSurrogate = t.isHighSurrogate = t.commonSuffixLength = t.commonPrefixLength = t.startsWithIgnoreCase = t.equalsIgnoreCase = t.isUpperAsciiLetter = t.isLowerAsciiLetter = t.isAsciiDigit = t.compareSubstringIgnoreCase = t.compareIgnoreCase = t.compareSubstring = t.compare = t.lastNonWhitespaceIndex = t.getLeadingWhitespace = t.firstNonWhitespaceIndex = t.splitLinesIncludeSeparators = t.splitLines = t.regExpLeadsToEndlessLoop = t.createRegExp = t.stripWildcards = t.convertSimple2RegExpPattern = t.rtrim = t.ltrim = t.trim = t.escapeRegExpCharacters = t.escape = t.htmlAttributeEncodeValue = t.format = t.isFalsyOrWhitespace = void 0;

    function d(k) {
      return !k || typeof k != 'string' ? !0 : k.trim().length === 0;
    }

    t.isFalsyOrWhitespace = d;
    const _ = /{(\d+)}/g;

    function p(k, ...U) {
      return U.length === 0 ? k : k.replace(_, function(V, $) {
        const ee = parseInt($, 10);
        return isNaN(ee) || ee < 0 || ee >= U.length ? V : U[ee];
      });
    }

    t.format = p;

    function c(k) {
      return k.replace(/[<>"'&]/g, U => {
        switch (U) {
          case'<':
            return '&lt;';
          case'>':
            return '&gt;';
          case'"':
            return '&quot;';
          case'\'':
            return '&apos;';
          case'&':
            return '&amp;';
        }
        return U;
      });
    }

    t.htmlAttributeEncodeValue = c;

    function o(k) {
      return k.replace(/[<>&]/g, function(U) {
        switch (U) {
          case'<':
            return '&lt;';
          case'>':
            return '&gt;';
          case'&':
            return '&amp;';
          default:
            return U;
        }
      });
    }

    t.escape = o;

    function L(k) {
      return k.replace(/[\\\{\}\*\+\?\|\^\$\.\[\]\(\)]/g, '\\$&');
    }

    t.escapeRegExpCharacters = L;

    function e(k, U = ' ') {
      const V = s(k, U);
      return l(V, U);
    }

    t.trim = e;

    function s(k, U) {
      if (!k || !U) return k;
      const V = U.length;
      if (V === 0 || k.length === 0) return k;
      let $ = 0;
      for (; k.indexOf(U, $) === $;) $ = $ + V;
      return k.substring($);
    }

    t.ltrim = s;

    function l(k, U) {
      if (!k || !U) return k;
      const V = U.length,
        $ = k.length;
      if (V === 0 || $ === 0) return k;
      let ee = $,
        ae = -1;
      for (; ae = k.lastIndexOf(U, ee - 1), !(ae === -1 || ae + V !== ee);) {
        if (ae === 0) return '';
        ee = ae;
      }
      return k.substring(0, ee);
    }

    t.rtrim = l;

    function u(k) {
      return k.replace(/[\-\\\{\}\+\?\|\^\$\.\,\[\]\(\)\#\s]/g, '\\$&')
        .replace(/[\*]/g, '.*');
    }

    t.convertSimple2RegExpPattern = u;

    function b(k) {
      return k.replace(/\*/g, '');
    }

    t.stripWildcards = b;

    function f(k, U, V = {}) {
      if (!k) throw new Error('Cannot create regex from empty string');
      U || (k = L(k)), V.wholeWord && (/\B/.test(k.charAt(0)) || (k = '\\b' + k), /\B/.test(k.charAt(k.length - 1)) || (k = k + '\\b'));
      let $ = '';
      return V.global && ($ += 'g'), V.matchCase || ($ += 'i'), V.multiline && ($ += 'm'), V.unicode && ($ += 'u'), new RegExp(k, $);
    }

    t.createRegExp = f;

    function y(k) {
      return k.source === '^' || k.source === '^$' || k.source === '$' || k.source === '^\\s*$' ? !1 : !!(k.exec('') && k.lastIndex === 0);
    }

    t.regExpLeadsToEndlessLoop = y;

    function w(k) {
      return k.split(/\r\n|\r|\n/);
    }

    t.splitLines = w;

    function E(k) {
      var U;
      const V = [],
        $ = k.split(/(\r\n|\r|\n)/);
      for (let ee = 0; ee < Math.ceil($.length / 2); ee++) V.push($[2 * ee] + ((U = $[2 * ee + 1]) !== null && U !== void 0 ? U : ''));
      return V;
    }

    t.splitLinesIncludeSeparators = E;

    function S(k) {
      for (let U = 0, V = k.length; U < V; U++) {
        const $ = k.charCodeAt(U);
        if ($ !== 32 && $ !== 9) return U;
      }
      return -1;
    }

    t.firstNonWhitespaceIndex = S;

    function C(k, U = 0, V = k.length) {
      for (let $ = U; $ < V; $++) {
        const ee = k.charCodeAt($);
        if (ee !== 32 && ee !== 9) return k.substring(U, $);
      }
      return k.substring(U, V);
    }

    t.getLeadingWhitespace = C;

    function r(k, U = k.length - 1) {
      for (let V = U; V >= 0; V--) {
        const $ = k.charCodeAt(V);
        if ($ !== 32 && $ !== 9) return V;
      }
      return -1;
    }

    t.lastNonWhitespaceIndex = r;

    function a(k, U) {
      return k < U ? -1 : k > U ? 1 : 0;
    }

    t.compare = a;

    function g(k, U, V = 0, $ = k.length, ee = 0, ae = U.length) {
      for (; V < $ && ee < ae; V++, ee++) {
        const de = k.charCodeAt(V),
          ue = U.charCodeAt(ee);
        if (de < ue) return -1;
        if (de > ue) return 1;
      }
      const he = $ - V,
        me = ae - ee;
      return he < me ? -1 : he > me ? 1 : 0;
    }

    t.compareSubstring = g;

    function m(k, U) {
      return h(k, U, 0, k.length, 0, U.length);
    }

    t.compareIgnoreCase = m;

    function h(k, U, V = 0, $ = k.length, ee = 0, ae = U.length) {
      for (; V < $ && ee < ae; V++, ee++) {
        let de = k.charCodeAt(V),
          ue = U.charCodeAt(ee);
        if (de === ue) continue;
        if (de >= 128 || ue >= 128) return g(k.toLowerCase(), U.toLowerCase(), V, $, ee, ae);
        N(de) && (de -= 32), N(ue) && (ue -= 32);
        const fe = de - ue;
        if (fe !== 0) return fe;
      }
      const he = $ - V,
        me = ae - ee;
      return he < me ? -1 : he > me ? 1 : 0;
    }

    t.compareSubstringIgnoreCase = h;

    function v(k) {
      return k >= 48 && k <= 57;
    }

    t.isAsciiDigit = v;

    function N(k) {
      return k >= 97 && k <= 122;
    }

    t.isLowerAsciiLetter = N;

    function A(k) {
      return k >= 65 && k <= 90;
    }

    t.isUpperAsciiLetter = A;

    function D(k, U) {
      return k.length === U.length && h(k, U) === 0;
    }

    t.equalsIgnoreCase = D;

    function P(k, U) {
      const V = U.length;
      return U.length > k.length ? !1 : h(k, U, 0, V) === 0;
    }

    t.startsWithIgnoreCase = P;

    function T(k, U) {
      const V = Math.min(k.length, U.length);
      let $;
      for ($ = 0; $ < V; $++) if (k.charCodeAt($) !== U.charCodeAt($)) return $;
      return V;
    }

    t.commonPrefixLength = T;

    function I(k, U) {
      const V = Math.min(k.length, U.length);
      let $;
      const ee = k.length - 1,
        ae = U.length - 1;
      for ($ = 0; $ < V; $++) if (k.charCodeAt(ee - $) !== U.charCodeAt(ae - $)) return $;
      return V;
    }

    t.commonSuffixLength = I;

    function B(k) {
      return 55296 <= k && k <= 56319;
    }

    t.isHighSurrogate = B;

    function z(k) {
      return 56320 <= k && k <= 57343;
    }

    t.isLowSurrogate = z;

    function x(k, U) {
      return (k - 55296 << 10) + (U - 56320) + 65536;
    }

    t.computeCodePoint = x;

    function O(k, U, V) {
      const $ = k.charCodeAt(V);
      if (B($) && V + 1 < U) {
        const ee = k.charCodeAt(V + 1);
        if (z(ee)) return x($, ee);
      }
      return $;
    }

    t.getNextCodePoint = O;

    function F(k, U) {
      const V = k.charCodeAt(U - 1);
      if (z(V) && U > 1) {
        const $ = k.charCodeAt(U - 2);
        if (B($)) return x($, V);
      }
      return V;
    }

    class W {
      constructor(U, V = 0) {
        this._str = U, this._len = U.length, this._offset = V;
      }

      get offset() {
        return this._offset;
      }

      setOffset(U) {
        this._offset = U;
      }

      prevCodePoint() {
        const U = F(this._str, this._offset);
        return this._offset -= U >= 65536 ? 2 : 1, U;
      }

      nextCodePoint() {
        const U = O(this._str, this._len, this._offset);
        return this._offset += U >= 65536 ? 2 : 1, U;
      }

      eol() {
        return this._offset >= this._len;
      }
    }

    t.CodePointIterator = W;

    class H {
      constructor(U, V = 0) {
        this._iterator = new W(U, V);
      }

      get offset() {
        return this._iterator.offset;
      }

      nextGraphemeLength() {
        const U = Q.getInstance(),
          V = this._iterator,
          $ = V.offset;
        let ee = U.getGraphemeBreakType(V.nextCodePoint());
        for (; !V.eol();) {
          const ae = V.offset,
            he = U.getGraphemeBreakType(V.nextCodePoint());
          if (j(ee, he)) {
            V.setOffset(ae);
            break;
          }
          ee = he;
        }
        return V.offset - $;
      }

      prevGraphemeLength() {
        const U = Q.getInstance(),
          V = this._iterator,
          $ = V.offset;
        let ee = U.getGraphemeBreakType(V.prevCodePoint());
        for (; V.offset > 0;) {
          const ae = V.offset,
            he = U.getGraphemeBreakType(V.prevCodePoint());
          if (j(he, ee)) {
            V.setOffset(ae);
            break;
          }
          ee = he;
        }
        return $ - V.offset;
      }

      eol() {
        return this._iterator.eol();
      }
    }

    t.GraphemeIterator = H;

    function G(k, U) {
      return new H(k, U).nextGraphemeLength();
    }

    t.nextCharLength = G;

    function ne(k, U) {
      return new H(k, U).prevGraphemeLength();
    }

    t.prevCharLength = ne;

    function se(k, U) {
      U > 0 && z(k.charCodeAt(U)) && U--;
      const V = U + G(k, U);
      return [V - ne(k, V), V];
    }

    t.getCharContainingOffset = se;
    let n;

    function be() {
      return /(?:[\u05BE\u05C0\u05C3\u05C6\u05D0-\u05F4\u0608\u060B\u060D\u061B-\u064A\u066D-\u066F\u0671-\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u0710\u0712-\u072F\u074D-\u07A5\u07B1-\u07EA\u07F4\u07F5\u07FA\u07FE-\u0815\u081A\u0824\u0828\u0830-\u0858\u085E-\u088E\u08A0-\u08C9\u200F\uFB1D\uFB1F-\uFB28\uFB2A-\uFD3D\uFD50-\uFDC7\uFDF0-\uFDFC\uFE70-\uFEFC]|\uD802[\uDC00-\uDD1B\uDD20-\uDE00\uDE10-\uDE35\uDE40-\uDEE4\uDEEB-\uDF35\uDF40-\uDFFF]|\uD803[\uDC00-\uDD23\uDE80-\uDEA9\uDEAD-\uDF45\uDF51-\uDF81\uDF86-\uDFF6]|\uD83A[\uDC00-\uDCCF\uDD00-\uDD43\uDD4B-\uDFFF]|\uD83B[\uDC00-\uDEBB])/;
    }

    function pe(k) {
      return n || (n = be()), n.test(k);
    }

    t.containsRTL = pe;
    const Le = /^[\t\n\r\x20-\x7E]*$/;

    function we(k) {
      return Le.test(k);
    }

    t.isBasicASCII = we, t.UNUSUAL_LINE_TERMINATORS = /[\u2028\u2029]/;

    function Ce(k) {
      return t.UNUSUAL_LINE_TERMINATORS.test(k);
    }

    t.containsUnusualLineTerminators = Ce;

    function Se(k) {
      return k >= 11904 && k <= 55215 || k >= 63744 && k <= 64255 || k >= 65281 && k <= 65374;
    }

    t.isFullWidthCharacter = Se;

    function _e(k) {
      return k >= 127462 && k <= 127487 || k === 8986 || k === 8987 || k === 9200 || k === 9203 || k >= 9728 && k <= 10175 || k === 11088 || k === 11093 || k >= 127744 && k <= 128591 || k >= 128640 && k <= 128764 || k >= 128992 && k <= 129008 || k >= 129280 && k <= 129535 || k >= 129648 && k <= 129782;
    }

    t.isEmojiImprecise = _e, t.UTF8_BOM_CHARACTER = '\uFEFF';

    function ye(k) {
      return !!(k && k.length > 0 && k.charCodeAt(0) === 65279);
    }

    t.startsWithUTF8BOM = ye;

    function Ee(k, U = !1) {
      return k ? (U && (k = k.replace(/\\./g, '')), k.toLowerCase() !== k) : !1;
    }

    t.containsUppercaseCharacter = Ee;

    function K(k) {
      return k = k % (2 * 26), k < 26 ? String.fromCharCode(97 + k) : String.fromCharCode(65 + k - 26);
    }

    t.singleLetterHash = K;

    function j(k, U) {
      return k === 0 ? U !== 5 && U !== 7 : k === 2 && U === 3 ? !1 : k === 4 || k === 2 || k === 3 || U === 4 || U === 2 || U === 3 ? !0 : !(k === 8 && (U === 8 || U === 9 || U === 11 || U === 12) || (k === 11 || k === 9) && (U === 9 || U === 10) || (k === 12 || k === 10) && U === 10 || U === 5 || U === 13 || U === 7 || k === 1 || k === 13 && U === 14 || k === 6 && U === 6);
    }

    class Q {
      constructor() {
        this._data = Y();
      }

      static getInstance() {
        return Q._INSTANCE || (Q._INSTANCE = new Q), Q._INSTANCE;
      }

      getGraphemeBreakType(U) {
        if (U < 32) return U === 10 ? 3 : U === 13 ? 2 : 4;
        if (U < 127) return 0;
        const V = this._data,
          $ = V.length / 3;
        let ee = 1;
        for (; ee <= $;) if (U < V[3 * ee]) ee = 2 * ee; else if (U > V[3 * ee + 1]) ee = 2 * ee + 1; else return V[3 * ee + 2];
        return 0;
      }
    }

    Q._INSTANCE = null;

    function Y() {
      return JSON.parse('[0,0,0,51229,51255,12,44061,44087,12,127462,127487,6,7083,7085,5,47645,47671,12,54813,54839,12,128678,128678,14,3270,3270,5,9919,9923,14,45853,45879,12,49437,49463,12,53021,53047,12,71216,71218,7,128398,128399,14,129360,129374,14,2519,2519,5,4448,4519,9,9742,9742,14,12336,12336,14,44957,44983,12,46749,46775,12,48541,48567,12,50333,50359,12,52125,52151,12,53917,53943,12,69888,69890,5,73018,73018,5,127990,127990,14,128558,128559,14,128759,128760,14,129653,129655,14,2027,2035,5,2891,2892,7,3761,3761,5,6683,6683,5,8293,8293,4,9825,9826,14,9999,9999,14,43452,43453,5,44509,44535,12,45405,45431,12,46301,46327,12,47197,47223,12,48093,48119,12,48989,49015,12,49885,49911,12,50781,50807,12,51677,51703,12,52573,52599,12,53469,53495,12,54365,54391,12,65279,65279,4,70471,70472,7,72145,72147,7,119173,119179,5,127799,127818,14,128240,128244,14,128512,128512,14,128652,128652,14,128721,128722,14,129292,129292,14,129445,129450,14,129734,129743,14,1476,1477,5,2366,2368,7,2750,2752,7,3076,3076,5,3415,3415,5,4141,4144,5,6109,6109,5,6964,6964,5,7394,7400,5,9197,9198,14,9770,9770,14,9877,9877,14,9968,9969,14,10084,10084,14,43052,43052,5,43713,43713,5,44285,44311,12,44733,44759,12,45181,45207,12,45629,45655,12,46077,46103,12,46525,46551,12,46973,46999,12,47421,47447,12,47869,47895,12,48317,48343,12,48765,48791,12,49213,49239,12,49661,49687,12,50109,50135,12,50557,50583,12,51005,51031,12,51453,51479,12,51901,51927,12,52349,52375,12,52797,52823,12,53245,53271,12,53693,53719,12,54141,54167,12,54589,54615,12,55037,55063,12,69506,69509,5,70191,70193,5,70841,70841,7,71463,71467,5,72330,72342,5,94031,94031,5,123628,123631,5,127763,127765,14,127941,127941,14,128043,128062,14,128302,128317,14,128465,128467,14,128539,128539,14,128640,128640,14,128662,128662,14,128703,128703,14,128745,128745,14,129004,129007,14,129329,129330,14,129402,129402,14,129483,129483,14,129686,129704,14,130048,131069,14,173,173,4,1757,1757,1,2200,2207,5,2434,2435,7,2631,2632,5,2817,2817,5,3008,3008,5,3201,3201,5,3387,3388,5,3542,3542,5,3902,3903,7,4190,4192,5,6002,6003,5,6439,6440,5,6765,6770,7,7019,7027,5,7154,7155,7,8205,8205,13,8505,8505,14,9654,9654,14,9757,9757,14,9792,9792,14,9852,9853,14,9890,9894,14,9937,9937,14,9981,9981,14,10035,10036,14,11035,11036,14,42654,42655,5,43346,43347,7,43587,43587,5,44006,44007,7,44173,44199,12,44397,44423,12,44621,44647,12,44845,44871,12,45069,45095,12,45293,45319,12,45517,45543,12,45741,45767,12,45965,45991,12,46189,46215,12,46413,46439,12,46637,46663,12,46861,46887,12,47085,47111,12,47309,47335,12,47533,47559,12,47757,47783,12,47981,48007,12,48205,48231,12,48429,48455,12,48653,48679,12,48877,48903,12,49101,49127,12,49325,49351,12,49549,49575,12,49773,49799,12,49997,50023,12,50221,50247,12,50445,50471,12,50669,50695,12,50893,50919,12,51117,51143,12,51341,51367,12,51565,51591,12,51789,51815,12,52013,52039,12,52237,52263,12,52461,52487,12,52685,52711,12,52909,52935,12,53133,53159,12,53357,53383,12,53581,53607,12,53805,53831,12,54029,54055,12,54253,54279,12,54477,54503,12,54701,54727,12,54925,54951,12,55149,55175,12,68101,68102,5,69762,69762,7,70067,70069,7,70371,70378,5,70720,70721,7,71087,71087,5,71341,71341,5,71995,71996,5,72249,72249,7,72850,72871,5,73109,73109,5,118576,118598,5,121505,121519,5,127245,127247,14,127568,127569,14,127777,127777,14,127872,127891,14,127956,127967,14,128015,128016,14,128110,128172,14,128259,128259,14,128367,128368,14,128424,128424,14,128488,128488,14,128530,128532,14,128550,128551,14,128566,128566,14,128647,128647,14,128656,128656,14,128667,128673,14,128691,128693,14,128715,128715,14,128728,128732,14,128752,128752,14,128765,128767,14,129096,129103,14,129311,129311,14,129344,129349,14,129394,129394,14,129413,129425,14,129466,129471,14,129511,129535,14,129664,129666,14,129719,129722,14,129760,129767,14,917536,917631,5,13,13,2,1160,1161,5,1564,1564,4,1807,1807,1,2085,2087,5,2307,2307,7,2382,2383,7,2497,2500,5,2563,2563,7,2677,2677,5,2763,2764,7,2879,2879,5,2914,2915,5,3021,3021,5,3142,3144,5,3263,3263,5,3285,3286,5,3398,3400,7,3530,3530,5,3633,3633,5,3864,3865,5,3974,3975,5,4155,4156,7,4229,4230,5,5909,5909,7,6078,6085,7,6277,6278,5,6451,6456,7,6744,6750,5,6846,6846,5,6972,6972,5,7074,7077,5,7146,7148,7,7222,7223,5,7416,7417,5,8234,8238,4,8417,8417,5,9000,9000,14,9203,9203,14,9730,9731,14,9748,9749,14,9762,9763,14,9776,9783,14,9800,9811,14,9831,9831,14,9872,9873,14,9882,9882,14,9900,9903,14,9929,9933,14,9941,9960,14,9974,9974,14,9989,9989,14,10006,10006,14,10062,10062,14,10160,10160,14,11647,11647,5,12953,12953,14,43019,43019,5,43232,43249,5,43443,43443,5,43567,43568,7,43696,43696,5,43765,43765,7,44013,44013,5,44117,44143,12,44229,44255,12,44341,44367,12,44453,44479,12,44565,44591,12,44677,44703,12,44789,44815,12,44901,44927,12,45013,45039,12,45125,45151,12,45237,45263,12,45349,45375,12,45461,45487,12,45573,45599,12,45685,45711,12,45797,45823,12,45909,45935,12,46021,46047,12,46133,46159,12,46245,46271,12,46357,46383,12,46469,46495,12,46581,46607,12,46693,46719,12,46805,46831,12,46917,46943,12,47029,47055,12,47141,47167,12,47253,47279,12,47365,47391,12,47477,47503,12,47589,47615,12,47701,47727,12,47813,47839,12,47925,47951,12,48037,48063,12,48149,48175,12,48261,48287,12,48373,48399,12,48485,48511,12,48597,48623,12,48709,48735,12,48821,48847,12,48933,48959,12,49045,49071,12,49157,49183,12,49269,49295,12,49381,49407,12,49493,49519,12,49605,49631,12,49717,49743,12,49829,49855,12,49941,49967,12,50053,50079,12,50165,50191,12,50277,50303,12,50389,50415,12,50501,50527,12,50613,50639,12,50725,50751,12,50837,50863,12,50949,50975,12,51061,51087,12,51173,51199,12,51285,51311,12,51397,51423,12,51509,51535,12,51621,51647,12,51733,51759,12,51845,51871,12,51957,51983,12,52069,52095,12,52181,52207,12,52293,52319,12,52405,52431,12,52517,52543,12,52629,52655,12,52741,52767,12,52853,52879,12,52965,52991,12,53077,53103,12,53189,53215,12,53301,53327,12,53413,53439,12,53525,53551,12,53637,53663,12,53749,53775,12,53861,53887,12,53973,53999,12,54085,54111,12,54197,54223,12,54309,54335,12,54421,54447,12,54533,54559,12,54645,54671,12,54757,54783,12,54869,54895,12,54981,55007,12,55093,55119,12,55243,55291,10,66045,66045,5,68325,68326,5,69688,69702,5,69817,69818,5,69957,69958,7,70089,70092,5,70198,70199,5,70462,70462,5,70502,70508,5,70750,70750,5,70846,70846,7,71100,71101,5,71230,71230,7,71351,71351,5,71737,71738,5,72000,72000,7,72160,72160,5,72273,72278,5,72752,72758,5,72882,72883,5,73031,73031,5,73461,73462,7,94192,94193,7,119149,119149,7,121403,121452,5,122915,122916,5,126980,126980,14,127358,127359,14,127535,127535,14,127759,127759,14,127771,127771,14,127792,127793,14,127825,127867,14,127897,127899,14,127945,127945,14,127985,127986,14,128000,128007,14,128021,128021,14,128066,128100,14,128184,128235,14,128249,128252,14,128266,128276,14,128335,128335,14,128379,128390,14,128407,128419,14,128444,128444,14,128481,128481,14,128499,128499,14,128526,128526,14,128536,128536,14,128543,128543,14,128556,128556,14,128564,128564,14,128577,128580,14,128643,128645,14,128649,128649,14,128654,128654,14,128660,128660,14,128664,128664,14,128675,128675,14,128686,128689,14,128695,128696,14,128705,128709,14,128717,128719,14,128725,128725,14,128736,128741,14,128747,128748,14,128755,128755,14,128762,128762,14,128981,128991,14,129009,129023,14,129160,129167,14,129296,129304,14,129320,129327,14,129340,129342,14,129356,129356,14,129388,129392,14,129399,129400,14,129404,129407,14,129432,129442,14,129454,129455,14,129473,129474,14,129485,129487,14,129648,129651,14,129659,129660,14,129671,129679,14,129709,129711,14,129728,129730,14,129751,129753,14,129776,129782,14,917505,917505,4,917760,917999,5,10,10,3,127,159,4,768,879,5,1471,1471,5,1536,1541,1,1648,1648,5,1767,1768,5,1840,1866,5,2070,2073,5,2137,2139,5,2274,2274,1,2363,2363,7,2377,2380,7,2402,2403,5,2494,2494,5,2507,2508,7,2558,2558,5,2622,2624,7,2641,2641,5,2691,2691,7,2759,2760,5,2786,2787,5,2876,2876,5,2881,2884,5,2901,2902,5,3006,3006,5,3014,3016,7,3072,3072,5,3134,3136,5,3157,3158,5,3260,3260,5,3266,3266,5,3274,3275,7,3328,3329,5,3391,3392,7,3405,3405,5,3457,3457,5,3536,3537,7,3551,3551,5,3636,3642,5,3764,3772,5,3895,3895,5,3967,3967,7,3993,4028,5,4146,4151,5,4182,4183,7,4226,4226,5,4253,4253,5,4957,4959,5,5940,5940,7,6070,6070,7,6087,6088,7,6158,6158,4,6432,6434,5,6448,6449,7,6679,6680,5,6742,6742,5,6754,6754,5,6783,6783,5,6912,6915,5,6966,6970,5,6978,6978,5,7042,7042,7,7080,7081,5,7143,7143,7,7150,7150,7,7212,7219,5,7380,7392,5,7412,7412,5,8203,8203,4,8232,8232,4,8265,8265,14,8400,8412,5,8421,8432,5,8617,8618,14,9167,9167,14,9200,9200,14,9410,9410,14,9723,9726,14,9733,9733,14,9745,9745,14,9752,9752,14,9760,9760,14,9766,9766,14,9774,9774,14,9786,9786,14,9794,9794,14,9823,9823,14,9828,9828,14,9833,9850,14,9855,9855,14,9875,9875,14,9880,9880,14,9885,9887,14,9896,9897,14,9906,9916,14,9926,9927,14,9935,9935,14,9939,9939,14,9962,9962,14,9972,9972,14,9978,9978,14,9986,9986,14,9997,9997,14,10002,10002,14,10017,10017,14,10055,10055,14,10071,10071,14,10133,10135,14,10548,10549,14,11093,11093,14,12330,12333,5,12441,12442,5,42608,42610,5,43010,43010,5,43045,43046,5,43188,43203,7,43302,43309,5,43392,43394,5,43446,43449,5,43493,43493,5,43571,43572,7,43597,43597,7,43703,43704,5,43756,43757,5,44003,44004,7,44009,44010,7,44033,44059,12,44089,44115,12,44145,44171,12,44201,44227,12,44257,44283,12,44313,44339,12,44369,44395,12,44425,44451,12,44481,44507,12,44537,44563,12,44593,44619,12,44649,44675,12,44705,44731,12,44761,44787,12,44817,44843,12,44873,44899,12,44929,44955,12,44985,45011,12,45041,45067,12,45097,45123,12,45153,45179,12,45209,45235,12,45265,45291,12,45321,45347,12,45377,45403,12,45433,45459,12,45489,45515,12,45545,45571,12,45601,45627,12,45657,45683,12,45713,45739,12,45769,45795,12,45825,45851,12,45881,45907,12,45937,45963,12,45993,46019,12,46049,46075,12,46105,46131,12,46161,46187,12,46217,46243,12,46273,46299,12,46329,46355,12,46385,46411,12,46441,46467,12,46497,46523,12,46553,46579,12,46609,46635,12,46665,46691,12,46721,46747,12,46777,46803,12,46833,46859,12,46889,46915,12,46945,46971,12,47001,47027,12,47057,47083,12,47113,47139,12,47169,47195,12,47225,47251,12,47281,47307,12,47337,47363,12,47393,47419,12,47449,47475,12,47505,47531,12,47561,47587,12,47617,47643,12,47673,47699,12,47729,47755,12,47785,47811,12,47841,47867,12,47897,47923,12,47953,47979,12,48009,48035,12,48065,48091,12,48121,48147,12,48177,48203,12,48233,48259,12,48289,48315,12,48345,48371,12,48401,48427,12,48457,48483,12,48513,48539,12,48569,48595,12,48625,48651,12,48681,48707,12,48737,48763,12,48793,48819,12,48849,48875,12,48905,48931,12,48961,48987,12,49017,49043,12,49073,49099,12,49129,49155,12,49185,49211,12,49241,49267,12,49297,49323,12,49353,49379,12,49409,49435,12,49465,49491,12,49521,49547,12,49577,49603,12,49633,49659,12,49689,49715,12,49745,49771,12,49801,49827,12,49857,49883,12,49913,49939,12,49969,49995,12,50025,50051,12,50081,50107,12,50137,50163,12,50193,50219,12,50249,50275,12,50305,50331,12,50361,50387,12,50417,50443,12,50473,50499,12,50529,50555,12,50585,50611,12,50641,50667,12,50697,50723,12,50753,50779,12,50809,50835,12,50865,50891,12,50921,50947,12,50977,51003,12,51033,51059,12,51089,51115,12,51145,51171,12,51201,51227,12,51257,51283,12,51313,51339,12,51369,51395,12,51425,51451,12,51481,51507,12,51537,51563,12,51593,51619,12,51649,51675,12,51705,51731,12,51761,51787,12,51817,51843,12,51873,51899,12,51929,51955,12,51985,52011,12,52041,52067,12,52097,52123,12,52153,52179,12,52209,52235,12,52265,52291,12,52321,52347,12,52377,52403,12,52433,52459,12,52489,52515,12,52545,52571,12,52601,52627,12,52657,52683,12,52713,52739,12,52769,52795,12,52825,52851,12,52881,52907,12,52937,52963,12,52993,53019,12,53049,53075,12,53105,53131,12,53161,53187,12,53217,53243,12,53273,53299,12,53329,53355,12,53385,53411,12,53441,53467,12,53497,53523,12,53553,53579,12,53609,53635,12,53665,53691,12,53721,53747,12,53777,53803,12,53833,53859,12,53889,53915,12,53945,53971,12,54001,54027,12,54057,54083,12,54113,54139,12,54169,54195,12,54225,54251,12,54281,54307,12,54337,54363,12,54393,54419,12,54449,54475,12,54505,54531,12,54561,54587,12,54617,54643,12,54673,54699,12,54729,54755,12,54785,54811,12,54841,54867,12,54897,54923,12,54953,54979,12,55009,55035,12,55065,55091,12,55121,55147,12,55177,55203,12,65024,65039,5,65520,65528,4,66422,66426,5,68152,68154,5,69291,69292,5,69633,69633,5,69747,69748,5,69811,69814,5,69826,69826,5,69932,69932,7,70016,70017,5,70079,70080,7,70095,70095,5,70196,70196,5,70367,70367,5,70402,70403,7,70464,70464,5,70487,70487,5,70709,70711,7,70725,70725,7,70833,70834,7,70843,70844,7,70849,70849,7,71090,71093,5,71103,71104,5,71227,71228,7,71339,71339,5,71344,71349,5,71458,71461,5,71727,71735,5,71985,71989,7,71998,71998,5,72002,72002,7,72154,72155,5,72193,72202,5,72251,72254,5,72281,72283,5,72344,72345,5,72766,72766,7,72874,72880,5,72885,72886,5,73023,73029,5,73104,73105,5,73111,73111,5,92912,92916,5,94095,94098,5,113824,113827,4,119142,119142,7,119155,119162,4,119362,119364,5,121476,121476,5,122888,122904,5,123184,123190,5,125252,125258,5,127183,127183,14,127340,127343,14,127377,127386,14,127491,127503,14,127548,127551,14,127744,127756,14,127761,127761,14,127769,127769,14,127773,127774,14,127780,127788,14,127796,127797,14,127820,127823,14,127869,127869,14,127894,127895,14,127902,127903,14,127943,127943,14,127947,127950,14,127972,127972,14,127988,127988,14,127992,127994,14,128009,128011,14,128019,128019,14,128023,128041,14,128064,128064,14,128102,128107,14,128174,128181,14,128238,128238,14,128246,128247,14,128254,128254,14,128264,128264,14,128278,128299,14,128329,128330,14,128348,128359,14,128371,128377,14,128392,128393,14,128401,128404,14,128421,128421,14,128433,128434,14,128450,128452,14,128476,128478,14,128483,128483,14,128495,128495,14,128506,128506,14,128519,128520,14,128528,128528,14,128534,128534,14,128538,128538,14,128540,128542,14,128544,128549,14,128552,128555,14,128557,128557,14,128560,128563,14,128565,128565,14,128567,128576,14,128581,128591,14,128641,128642,14,128646,128646,14,128648,128648,14,128650,128651,14,128653,128653,14,128655,128655,14,128657,128659,14,128661,128661,14,128663,128663,14,128665,128666,14,128674,128674,14,128676,128677,14,128679,128685,14,128690,128690,14,128694,128694,14,128697,128702,14,128704,128704,14,128710,128714,14,128716,128716,14,128720,128720,14,128723,128724,14,128726,128727,14,128733,128735,14,128742,128744,14,128746,128746,14,128749,128751,14,128753,128754,14,128756,128758,14,128761,128761,14,128763,128764,14,128884,128895,14,128992,129003,14,129008,129008,14,129036,129039,14,129114,129119,14,129198,129279,14,129293,129295,14,129305,129310,14,129312,129319,14,129328,129328,14,129331,129338,14,129343,129343,14,129351,129355,14,129357,129359,14,129375,129387,14,129393,129393,14,129395,129398,14,129401,129401,14,129403,129403,14,129408,129412,14,129426,129431,14,129443,129444,14,129451,129453,14,129456,129465,14,129472,129472,14,129475,129482,14,129484,129484,14,129488,129510,14,129536,129647,14,129652,129652,14,129656,129658,14,129661,129663,14,129667,129670,14,129680,129685,14,129705,129708,14,129712,129718,14,129723,129727,14,129731,129733,14,129744,129750,14,129754,129759,14,129768,129775,14,129783,129791,14,917504,917504,4,917506,917535,4,917632,917759,4,918000,921599,4,0,9,4,11,12,4,14,31,4,169,169,14,174,174,14,1155,1159,5,1425,1469,5,1473,1474,5,1479,1479,5,1552,1562,5,1611,1631,5,1750,1756,5,1759,1764,5,1770,1773,5,1809,1809,5,1958,1968,5,2045,2045,5,2075,2083,5,2089,2093,5,2192,2193,1,2250,2273,5,2275,2306,5,2362,2362,5,2364,2364,5,2369,2376,5,2381,2381,5,2385,2391,5,2433,2433,5,2492,2492,5,2495,2496,7,2503,2504,7,2509,2509,5,2530,2531,5,2561,2562,5,2620,2620,5,2625,2626,5,2635,2637,5,2672,2673,5,2689,2690,5,2748,2748,5,2753,2757,5,2761,2761,7,2765,2765,5,2810,2815,5,2818,2819,7,2878,2878,5,2880,2880,7,2887,2888,7,2893,2893,5,2903,2903,5,2946,2946,5,3007,3007,7,3009,3010,7,3018,3020,7,3031,3031,5,3073,3075,7,3132,3132,5,3137,3140,7,3146,3149,5,3170,3171,5,3202,3203,7,3262,3262,7,3264,3265,7,3267,3268,7,3271,3272,7,3276,3277,5,3298,3299,5,3330,3331,7,3390,3390,5,3393,3396,5,3402,3404,7,3406,3406,1,3426,3427,5,3458,3459,7,3535,3535,5,3538,3540,5,3544,3550,7,3570,3571,7,3635,3635,7,3655,3662,5,3763,3763,7,3784,3789,5,3893,3893,5,3897,3897,5,3953,3966,5,3968,3972,5,3981,3991,5,4038,4038,5,4145,4145,7,4153,4154,5,4157,4158,5,4184,4185,5,4209,4212,5,4228,4228,7,4237,4237,5,4352,4447,8,4520,4607,10,5906,5908,5,5938,5939,5,5970,5971,5,6068,6069,5,6071,6077,5,6086,6086,5,6089,6099,5,6155,6157,5,6159,6159,5,6313,6313,5,6435,6438,7,6441,6443,7,6450,6450,5,6457,6459,5,6681,6682,7,6741,6741,7,6743,6743,7,6752,6752,5,6757,6764,5,6771,6780,5,6832,6845,5,6847,6862,5,6916,6916,7,6965,6965,5,6971,6971,7,6973,6977,7,6979,6980,7,7040,7041,5,7073,7073,7,7078,7079,7,7082,7082,7,7142,7142,5,7144,7145,5,7149,7149,5,7151,7153,5,7204,7211,7,7220,7221,7,7376,7378,5,7393,7393,7,7405,7405,5,7415,7415,7,7616,7679,5,8204,8204,5,8206,8207,4,8233,8233,4,8252,8252,14,8288,8292,4,8294,8303,4,8413,8416,5,8418,8420,5,8482,8482,14,8596,8601,14,8986,8987,14,9096,9096,14,9193,9196,14,9199,9199,14,9201,9202,14,9208,9210,14,9642,9643,14,9664,9664,14,9728,9729,14,9732,9732,14,9735,9741,14,9743,9744,14,9746,9746,14,9750,9751,14,9753,9756,14,9758,9759,14,9761,9761,14,9764,9765,14,9767,9769,14,9771,9773,14,9775,9775,14,9784,9785,14,9787,9791,14,9793,9793,14,9795,9799,14,9812,9822,14,9824,9824,14,9827,9827,14,9829,9830,14,9832,9832,14,9851,9851,14,9854,9854,14,9856,9861,14,9874,9874,14,9876,9876,14,9878,9879,14,9881,9881,14,9883,9884,14,9888,9889,14,9895,9895,14,9898,9899,14,9904,9905,14,9917,9918,14,9924,9925,14,9928,9928,14,9934,9934,14,9936,9936,14,9938,9938,14,9940,9940,14,9961,9961,14,9963,9967,14,9970,9971,14,9973,9973,14,9975,9977,14,9979,9980,14,9982,9985,14,9987,9988,14,9992,9996,14,9998,9998,14,10000,10001,14,10004,10004,14,10013,10013,14,10024,10024,14,10052,10052,14,10060,10060,14,10067,10069,14,10083,10083,14,10085,10087,14,10145,10145,14,10175,10175,14,11013,11015,14,11088,11088,14,11503,11505,5,11744,11775,5,12334,12335,5,12349,12349,14,12951,12951,14,42607,42607,5,42612,42621,5,42736,42737,5,43014,43014,5,43043,43044,7,43047,43047,7,43136,43137,7,43204,43205,5,43263,43263,5,43335,43345,5,43360,43388,8,43395,43395,7,43444,43445,7,43450,43451,7,43454,43456,7,43561,43566,5,43569,43570,5,43573,43574,5,43596,43596,5,43644,43644,5,43698,43700,5,43710,43711,5,43755,43755,7,43758,43759,7,43766,43766,5,44005,44005,5,44008,44008,5,44012,44012,7,44032,44032,11,44060,44060,11,44088,44088,11,44116,44116,11,44144,44144,11,44172,44172,11,44200,44200,11,44228,44228,11,44256,44256,11,44284,44284,11,44312,44312,11,44340,44340,11,44368,44368,11,44396,44396,11,44424,44424,11,44452,44452,11,44480,44480,11,44508,44508,11,44536,44536,11,44564,44564,11,44592,44592,11,44620,44620,11,44648,44648,11,44676,44676,11,44704,44704,11,44732,44732,11,44760,44760,11,44788,44788,11,44816,44816,11,44844,44844,11,44872,44872,11,44900,44900,11,44928,44928,11,44956,44956,11,44984,44984,11,45012,45012,11,45040,45040,11,45068,45068,11,45096,45096,11,45124,45124,11,45152,45152,11,45180,45180,11,45208,45208,11,45236,45236,11,45264,45264,11,45292,45292,11,45320,45320,11,45348,45348,11,45376,45376,11,45404,45404,11,45432,45432,11,45460,45460,11,45488,45488,11,45516,45516,11,45544,45544,11,45572,45572,11,45600,45600,11,45628,45628,11,45656,45656,11,45684,45684,11,45712,45712,11,45740,45740,11,45768,45768,11,45796,45796,11,45824,45824,11,45852,45852,11,45880,45880,11,45908,45908,11,45936,45936,11,45964,45964,11,45992,45992,11,46020,46020,11,46048,46048,11,46076,46076,11,46104,46104,11,46132,46132,11,46160,46160,11,46188,46188,11,46216,46216,11,46244,46244,11,46272,46272,11,46300,46300,11,46328,46328,11,46356,46356,11,46384,46384,11,46412,46412,11,46440,46440,11,46468,46468,11,46496,46496,11,46524,46524,11,46552,46552,11,46580,46580,11,46608,46608,11,46636,46636,11,46664,46664,11,46692,46692,11,46720,46720,11,46748,46748,11,46776,46776,11,46804,46804,11,46832,46832,11,46860,46860,11,46888,46888,11,46916,46916,11,46944,46944,11,46972,46972,11,47000,47000,11,47028,47028,11,47056,47056,11,47084,47084,11,47112,47112,11,47140,47140,11,47168,47168,11,47196,47196,11,47224,47224,11,47252,47252,11,47280,47280,11,47308,47308,11,47336,47336,11,47364,47364,11,47392,47392,11,47420,47420,11,47448,47448,11,47476,47476,11,47504,47504,11,47532,47532,11,47560,47560,11,47588,47588,11,47616,47616,11,47644,47644,11,47672,47672,11,47700,47700,11,47728,47728,11,47756,47756,11,47784,47784,11,47812,47812,11,47840,47840,11,47868,47868,11,47896,47896,11,47924,47924,11,47952,47952,11,47980,47980,11,48008,48008,11,48036,48036,11,48064,48064,11,48092,48092,11,48120,48120,11,48148,48148,11,48176,48176,11,48204,48204,11,48232,48232,11,48260,48260,11,48288,48288,11,48316,48316,11,48344,48344,11,48372,48372,11,48400,48400,11,48428,48428,11,48456,48456,11,48484,48484,11,48512,48512,11,48540,48540,11,48568,48568,11,48596,48596,11,48624,48624,11,48652,48652,11,48680,48680,11,48708,48708,11,48736,48736,11,48764,48764,11,48792,48792,11,48820,48820,11,48848,48848,11,48876,48876,11,48904,48904,11,48932,48932,11,48960,48960,11,48988,48988,11,49016,49016,11,49044,49044,11,49072,49072,11,49100,49100,11,49128,49128,11,49156,49156,11,49184,49184,11,49212,49212,11,49240,49240,11,49268,49268,11,49296,49296,11,49324,49324,11,49352,49352,11,49380,49380,11,49408,49408,11,49436,49436,11,49464,49464,11,49492,49492,11,49520,49520,11,49548,49548,11,49576,49576,11,49604,49604,11,49632,49632,11,49660,49660,11,49688,49688,11,49716,49716,11,49744,49744,11,49772,49772,11,49800,49800,11,49828,49828,11,49856,49856,11,49884,49884,11,49912,49912,11,49940,49940,11,49968,49968,11,49996,49996,11,50024,50024,11,50052,50052,11,50080,50080,11,50108,50108,11,50136,50136,11,50164,50164,11,50192,50192,11,50220,50220,11,50248,50248,11,50276,50276,11,50304,50304,11,50332,50332,11,50360,50360,11,50388,50388,11,50416,50416,11,50444,50444,11,50472,50472,11,50500,50500,11,50528,50528,11,50556,50556,11,50584,50584,11,50612,50612,11,50640,50640,11,50668,50668,11,50696,50696,11,50724,50724,11,50752,50752,11,50780,50780,11,50808,50808,11,50836,50836,11,50864,50864,11,50892,50892,11,50920,50920,11,50948,50948,11,50976,50976,11,51004,51004,11,51032,51032,11,51060,51060,11,51088,51088,11,51116,51116,11,51144,51144,11,51172,51172,11,51200,51200,11,51228,51228,11,51256,51256,11,51284,51284,11,51312,51312,11,51340,51340,11,51368,51368,11,51396,51396,11,51424,51424,11,51452,51452,11,51480,51480,11,51508,51508,11,51536,51536,11,51564,51564,11,51592,51592,11,51620,51620,11,51648,51648,11,51676,51676,11,51704,51704,11,51732,51732,11,51760,51760,11,51788,51788,11,51816,51816,11,51844,51844,11,51872,51872,11,51900,51900,11,51928,51928,11,51956,51956,11,51984,51984,11,52012,52012,11,52040,52040,11,52068,52068,11,52096,52096,11,52124,52124,11,52152,52152,11,52180,52180,11,52208,52208,11,52236,52236,11,52264,52264,11,52292,52292,11,52320,52320,11,52348,52348,11,52376,52376,11,52404,52404,11,52432,52432,11,52460,52460,11,52488,52488,11,52516,52516,11,52544,52544,11,52572,52572,11,52600,52600,11,52628,52628,11,52656,52656,11,52684,52684,11,52712,52712,11,52740,52740,11,52768,52768,11,52796,52796,11,52824,52824,11,52852,52852,11,52880,52880,11,52908,52908,11,52936,52936,11,52964,52964,11,52992,52992,11,53020,53020,11,53048,53048,11,53076,53076,11,53104,53104,11,53132,53132,11,53160,53160,11,53188,53188,11,53216,53216,11,53244,53244,11,53272,53272,11,53300,53300,11,53328,53328,11,53356,53356,11,53384,53384,11,53412,53412,11,53440,53440,11,53468,53468,11,53496,53496,11,53524,53524,11,53552,53552,11,53580,53580,11,53608,53608,11,53636,53636,11,53664,53664,11,53692,53692,11,53720,53720,11,53748,53748,11,53776,53776,11,53804,53804,11,53832,53832,11,53860,53860,11,53888,53888,11,53916,53916,11,53944,53944,11,53972,53972,11,54000,54000,11,54028,54028,11,54056,54056,11,54084,54084,11,54112,54112,11,54140,54140,11,54168,54168,11,54196,54196,11,54224,54224,11,54252,54252,11,54280,54280,11,54308,54308,11,54336,54336,11,54364,54364,11,54392,54392,11,54420,54420,11,54448,54448,11,54476,54476,11,54504,54504,11,54532,54532,11,54560,54560,11,54588,54588,11,54616,54616,11,54644,54644,11,54672,54672,11,54700,54700,11,54728,54728,11,54756,54756,11,54784,54784,11,54812,54812,11,54840,54840,11,54868,54868,11,54896,54896,11,54924,54924,11,54952,54952,11,54980,54980,11,55008,55008,11,55036,55036,11,55064,55064,11,55092,55092,11,55120,55120,11,55148,55148,11,55176,55176,11,55216,55238,9,64286,64286,5,65056,65071,5,65438,65439,5,65529,65531,4,66272,66272,5,68097,68099,5,68108,68111,5,68159,68159,5,68900,68903,5,69446,69456,5,69632,69632,7,69634,69634,7,69744,69744,5,69759,69761,5,69808,69810,7,69815,69816,7,69821,69821,1,69837,69837,1,69927,69931,5,69933,69940,5,70003,70003,5,70018,70018,7,70070,70078,5,70082,70083,1,70094,70094,7,70188,70190,7,70194,70195,7,70197,70197,7,70206,70206,5,70368,70370,7,70400,70401,5,70459,70460,5,70463,70463,7,70465,70468,7,70475,70477,7,70498,70499,7,70512,70516,5,70712,70719,5,70722,70724,5,70726,70726,5,70832,70832,5,70835,70840,5,70842,70842,5,70845,70845,5,70847,70848,5,70850,70851,5,71088,71089,7,71096,71099,7,71102,71102,7,71132,71133,5,71219,71226,5,71229,71229,5,71231,71232,5,71340,71340,7,71342,71343,7,71350,71350,7,71453,71455,5,71462,71462,7,71724,71726,7,71736,71736,7,71984,71984,5,71991,71992,7,71997,71997,7,71999,71999,1,72001,72001,1,72003,72003,5,72148,72151,5,72156,72159,7,72164,72164,7,72243,72248,5,72250,72250,1,72263,72263,5,72279,72280,7,72324,72329,1,72343,72343,7,72751,72751,7,72760,72765,5,72767,72767,5,72873,72873,7,72881,72881,7,72884,72884,7,73009,73014,5,73020,73021,5,73030,73030,1,73098,73102,7,73107,73108,7,73110,73110,7,73459,73460,5,78896,78904,4,92976,92982,5,94033,94087,7,94180,94180,5,113821,113822,5,118528,118573,5,119141,119141,5,119143,119145,5,119150,119154,5,119163,119170,5,119210,119213,5,121344,121398,5,121461,121461,5,121499,121503,5,122880,122886,5,122907,122913,5,122918,122922,5,123566,123566,5,125136,125142,5,126976,126979,14,126981,127182,14,127184,127231,14,127279,127279,14,127344,127345,14,127374,127374,14,127405,127461,14,127489,127490,14,127514,127514,14,127538,127546,14,127561,127567,14,127570,127743,14,127757,127758,14,127760,127760,14,127762,127762,14,127766,127768,14,127770,127770,14,127772,127772,14,127775,127776,14,127778,127779,14,127789,127791,14,127794,127795,14,127798,127798,14,127819,127819,14,127824,127824,14,127868,127868,14,127870,127871,14,127892,127893,14,127896,127896,14,127900,127901,14,127904,127940,14,127942,127942,14,127944,127944,14,127946,127946,14,127951,127955,14,127968,127971,14,127973,127984,14,127987,127987,14,127989,127989,14,127991,127991,14,127995,127999,5,128008,128008,14,128012,128014,14,128017,128018,14,128020,128020,14,128022,128022,14,128042,128042,14,128063,128063,14,128065,128065,14,128101,128101,14,128108,128109,14,128173,128173,14,128182,128183,14,128236,128237,14,128239,128239,14,128245,128245,14,128248,128248,14,128253,128253,14,128255,128258,14,128260,128263,14,128265,128265,14,128277,128277,14,128300,128301,14,128326,128328,14,128331,128334,14,128336,128347,14,128360,128366,14,128369,128370,14,128378,128378,14,128391,128391,14,128394,128397,14,128400,128400,14,128405,128406,14,128420,128420,14,128422,128423,14,128425,128432,14,128435,128443,14,128445,128449,14,128453,128464,14,128468,128475,14,128479,128480,14,128482,128482,14,128484,128487,14,128489,128494,14,128496,128498,14,128500,128505,14,128507,128511,14,128513,128518,14,128521,128525,14,128527,128527,14,128529,128529,14,128533,128533,14,128535,128535,14,128537,128537,14]');
    }

    function te(k, U) {
      if (k === 0) return 0;
      const V = ie(k, U);
      if (V !== void 0) return V;
      const $ = new W(U, k);
      return $.prevCodePoint(), $.offset;
    }

    t.getLeftDeleteOffset = te;

    function ie(k, U) {
      const V = new W(U, k);
      let $ = V.prevCodePoint();
      for (; re($) || $ === 65039 || $ === 8419;) {
        if (V.offset === 0) return;
        $ = V.prevCodePoint();
      }
      if (!_e($)) return;
      let ee = V.offset;
      return ee > 0 && V.prevCodePoint() === 8205 && (ee = V.offset), ee;
    }

    function re(k) {
      return 127995 <= k && k <= 127999;
    }

    t.noBreakWhitespace = '\xA0';

    class oe {
      constructor(U) {
        this.confusableDictionary = U;
      }

      static getInstance(U) {
        return i.cache.get(Array.from(U));
      }

      static getLocales() {
        return i._locales.value;
      }

      isAmbiguous(U) {
        return this.confusableDictionary.has(U);
      }

      getPrimaryConfusable(U) {
        return this.confusableDictionary.get(U);
      }

      getConfusableCodePoints() {
        return new Set(this.confusableDictionary.keys());
      }
    }

    t.AmbiguousCharacters = oe, i = oe, oe.ambiguousCharacterData = new R.Lazy(() => JSON.parse('{"_common":[8232,32,8233,32,5760,32,8192,32,8193,32,8194,32,8195,32,8196,32,8197,32,8198,32,8200,32,8201,32,8202,32,8287,32,8199,32,8239,32,2042,95,65101,95,65102,95,65103,95,8208,45,8209,45,8210,45,65112,45,1748,45,8259,45,727,45,8722,45,10134,45,11450,45,1549,44,1643,44,8218,44,184,44,42233,44,894,59,2307,58,2691,58,1417,58,1795,58,1796,58,5868,58,65072,58,6147,58,6153,58,8282,58,1475,58,760,58,42889,58,8758,58,720,58,42237,58,451,33,11601,33,660,63,577,63,2429,63,5038,63,42731,63,119149,46,8228,46,1793,46,1794,46,42510,46,68176,46,1632,46,1776,46,42232,46,1373,96,65287,96,8219,96,8242,96,1370,96,1523,96,8175,96,65344,96,900,96,8189,96,8125,96,8127,96,8190,96,697,96,884,96,712,96,714,96,715,96,756,96,699,96,701,96,700,96,702,96,42892,96,1497,96,2036,96,2037,96,5194,96,5836,96,94033,96,94034,96,65339,91,10088,40,10098,40,12308,40,64830,40,65341,93,10089,41,10099,41,12309,41,64831,41,10100,123,119060,123,10101,125,65342,94,8270,42,1645,42,8727,42,66335,42,5941,47,8257,47,8725,47,8260,47,9585,47,10187,47,10744,47,119354,47,12755,47,12339,47,11462,47,20031,47,12035,47,65340,92,65128,92,8726,92,10189,92,10741,92,10745,92,119311,92,119355,92,12756,92,20022,92,12034,92,42872,38,708,94,710,94,5869,43,10133,43,66203,43,8249,60,10094,60,706,60,119350,60,5176,60,5810,60,5120,61,11840,61,12448,61,42239,61,8250,62,10095,62,707,62,119351,62,5171,62,94015,62,8275,126,732,126,8128,126,8764,126,65372,124,65293,45,120784,50,120794,50,120804,50,120814,50,120824,50,130034,50,42842,50,423,50,1000,50,42564,50,5311,50,42735,50,119302,51,120785,51,120795,51,120805,51,120815,51,120825,51,130035,51,42923,51,540,51,439,51,42858,51,11468,51,1248,51,94011,51,71882,51,120786,52,120796,52,120806,52,120816,52,120826,52,130036,52,5070,52,71855,52,120787,53,120797,53,120807,53,120817,53,120827,53,130037,53,444,53,71867,53,120788,54,120798,54,120808,54,120818,54,120828,54,130038,54,11474,54,5102,54,71893,54,119314,55,120789,55,120799,55,120809,55,120819,55,120829,55,130039,55,66770,55,71878,55,2819,56,2538,56,2666,56,125131,56,120790,56,120800,56,120810,56,120820,56,120830,56,130040,56,547,56,546,56,66330,56,2663,57,2920,57,2541,57,3437,57,120791,57,120801,57,120811,57,120821,57,120831,57,130041,57,42862,57,11466,57,71884,57,71852,57,71894,57,9082,97,65345,97,119834,97,119886,97,119938,97,119990,97,120042,97,120094,97,120146,97,120198,97,120250,97,120302,97,120354,97,120406,97,120458,97,593,97,945,97,120514,97,120572,97,120630,97,120688,97,120746,97,65313,65,119808,65,119860,65,119912,65,119964,65,120016,65,120068,65,120120,65,120172,65,120224,65,120276,65,120328,65,120380,65,120432,65,913,65,120488,65,120546,65,120604,65,120662,65,120720,65,5034,65,5573,65,42222,65,94016,65,66208,65,119835,98,119887,98,119939,98,119991,98,120043,98,120095,98,120147,98,120199,98,120251,98,120303,98,120355,98,120407,98,120459,98,388,98,5071,98,5234,98,5551,98,65314,66,8492,66,119809,66,119861,66,119913,66,120017,66,120069,66,120121,66,120173,66,120225,66,120277,66,120329,66,120381,66,120433,66,42932,66,914,66,120489,66,120547,66,120605,66,120663,66,120721,66,5108,66,5623,66,42192,66,66178,66,66209,66,66305,66,65347,99,8573,99,119836,99,119888,99,119940,99,119992,99,120044,99,120096,99,120148,99,120200,99,120252,99,120304,99,120356,99,120408,99,120460,99,7428,99,1010,99,11429,99,43951,99,66621,99,128844,67,71922,67,71913,67,65315,67,8557,67,8450,67,8493,67,119810,67,119862,67,119914,67,119966,67,120018,67,120174,67,120226,67,120278,67,120330,67,120382,67,120434,67,1017,67,11428,67,5087,67,42202,67,66210,67,66306,67,66581,67,66844,67,8574,100,8518,100,119837,100,119889,100,119941,100,119993,100,120045,100,120097,100,120149,100,120201,100,120253,100,120305,100,120357,100,120409,100,120461,100,1281,100,5095,100,5231,100,42194,100,8558,68,8517,68,119811,68,119863,68,119915,68,119967,68,120019,68,120071,68,120123,68,120175,68,120227,68,120279,68,120331,68,120383,68,120435,68,5024,68,5598,68,5610,68,42195,68,8494,101,65349,101,8495,101,8519,101,119838,101,119890,101,119942,101,120046,101,120098,101,120150,101,120202,101,120254,101,120306,101,120358,101,120410,101,120462,101,43826,101,1213,101,8959,69,65317,69,8496,69,119812,69,119864,69,119916,69,120020,69,120072,69,120124,69,120176,69,120228,69,120280,69,120332,69,120384,69,120436,69,917,69,120492,69,120550,69,120608,69,120666,69,120724,69,11577,69,5036,69,42224,69,71846,69,71854,69,66182,69,119839,102,119891,102,119943,102,119995,102,120047,102,120099,102,120151,102,120203,102,120255,102,120307,102,120359,102,120411,102,120463,102,43829,102,42905,102,383,102,7837,102,1412,102,119315,70,8497,70,119813,70,119865,70,119917,70,120021,70,120073,70,120125,70,120177,70,120229,70,120281,70,120333,70,120385,70,120437,70,42904,70,988,70,120778,70,5556,70,42205,70,71874,70,71842,70,66183,70,66213,70,66853,70,65351,103,8458,103,119840,103,119892,103,119944,103,120048,103,120100,103,120152,103,120204,103,120256,103,120308,103,120360,103,120412,103,120464,103,609,103,7555,103,397,103,1409,103,119814,71,119866,71,119918,71,119970,71,120022,71,120074,71,120126,71,120178,71,120230,71,120282,71,120334,71,120386,71,120438,71,1292,71,5056,71,5107,71,42198,71,65352,104,8462,104,119841,104,119945,104,119997,104,120049,104,120101,104,120153,104,120205,104,120257,104,120309,104,120361,104,120413,104,120465,104,1211,104,1392,104,5058,104,65320,72,8459,72,8460,72,8461,72,119815,72,119867,72,119919,72,120023,72,120179,72,120231,72,120283,72,120335,72,120387,72,120439,72,919,72,120494,72,120552,72,120610,72,120668,72,120726,72,11406,72,5051,72,5500,72,42215,72,66255,72,731,105,9075,105,65353,105,8560,105,8505,105,8520,105,119842,105,119894,105,119946,105,119998,105,120050,105,120102,105,120154,105,120206,105,120258,105,120310,105,120362,105,120414,105,120466,105,120484,105,618,105,617,105,953,105,8126,105,890,105,120522,105,120580,105,120638,105,120696,105,120754,105,1110,105,42567,105,1231,105,43893,105,5029,105,71875,105,65354,106,8521,106,119843,106,119895,106,119947,106,119999,106,120051,106,120103,106,120155,106,120207,106,120259,106,120311,106,120363,106,120415,106,120467,106,1011,106,1112,106,65322,74,119817,74,119869,74,119921,74,119973,74,120025,74,120077,74,120129,74,120181,74,120233,74,120285,74,120337,74,120389,74,120441,74,42930,74,895,74,1032,74,5035,74,5261,74,42201,74,119844,107,119896,107,119948,107,120000,107,120052,107,120104,107,120156,107,120208,107,120260,107,120312,107,120364,107,120416,107,120468,107,8490,75,65323,75,119818,75,119870,75,119922,75,119974,75,120026,75,120078,75,120130,75,120182,75,120234,75,120286,75,120338,75,120390,75,120442,75,922,75,120497,75,120555,75,120613,75,120671,75,120729,75,11412,75,5094,75,5845,75,42199,75,66840,75,1472,108,8739,73,9213,73,65512,73,1633,108,1777,73,66336,108,125127,108,120783,73,120793,73,120803,73,120813,73,120823,73,130033,73,65321,73,8544,73,8464,73,8465,73,119816,73,119868,73,119920,73,120024,73,120128,73,120180,73,120232,73,120284,73,120336,73,120388,73,120440,73,65356,108,8572,73,8467,108,119845,108,119897,108,119949,108,120001,108,120053,108,120105,73,120157,73,120209,73,120261,73,120313,73,120365,73,120417,73,120469,73,448,73,120496,73,120554,73,120612,73,120670,73,120728,73,11410,73,1030,73,1216,73,1493,108,1503,108,1575,108,126464,108,126592,108,65166,108,65165,108,1994,108,11599,73,5825,73,42226,73,93992,73,66186,124,66313,124,119338,76,8556,76,8466,76,119819,76,119871,76,119923,76,120027,76,120079,76,120131,76,120183,76,120235,76,120287,76,120339,76,120391,76,120443,76,11472,76,5086,76,5290,76,42209,76,93974,76,71843,76,71858,76,66587,76,66854,76,65325,77,8559,77,8499,77,119820,77,119872,77,119924,77,120028,77,120080,77,120132,77,120184,77,120236,77,120288,77,120340,77,120392,77,120444,77,924,77,120499,77,120557,77,120615,77,120673,77,120731,77,1018,77,11416,77,5047,77,5616,77,5846,77,42207,77,66224,77,66321,77,119847,110,119899,110,119951,110,120003,110,120055,110,120107,110,120159,110,120211,110,120263,110,120315,110,120367,110,120419,110,120471,110,1400,110,1404,110,65326,78,8469,78,119821,78,119873,78,119925,78,119977,78,120029,78,120081,78,120185,78,120237,78,120289,78,120341,78,120393,78,120445,78,925,78,120500,78,120558,78,120616,78,120674,78,120732,78,11418,78,42208,78,66835,78,3074,111,3202,111,3330,111,3458,111,2406,111,2662,111,2790,111,3046,111,3174,111,3302,111,3430,111,3664,111,3792,111,4160,111,1637,111,1781,111,65359,111,8500,111,119848,111,119900,111,119952,111,120056,111,120108,111,120160,111,120212,111,120264,111,120316,111,120368,111,120420,111,120472,111,7439,111,7441,111,43837,111,959,111,120528,111,120586,111,120644,111,120702,111,120760,111,963,111,120532,111,120590,111,120648,111,120706,111,120764,111,11423,111,4351,111,1413,111,1505,111,1607,111,126500,111,126564,111,126596,111,65259,111,65260,111,65258,111,65257,111,1726,111,64428,111,64429,111,64427,111,64426,111,1729,111,64424,111,64425,111,64423,111,64422,111,1749,111,3360,111,4125,111,66794,111,71880,111,71895,111,66604,111,1984,79,2534,79,2918,79,12295,79,70864,79,71904,79,120782,79,120792,79,120802,79,120812,79,120822,79,130032,79,65327,79,119822,79,119874,79,119926,79,119978,79,120030,79,120082,79,120134,79,120186,79,120238,79,120290,79,120342,79,120394,79,120446,79,927,79,120502,79,120560,79,120618,79,120676,79,120734,79,11422,79,1365,79,11604,79,4816,79,2848,79,66754,79,42227,79,71861,79,66194,79,66219,79,66564,79,66838,79,9076,112,65360,112,119849,112,119901,112,119953,112,120005,112,120057,112,120109,112,120161,112,120213,112,120265,112,120317,112,120369,112,120421,112,120473,112,961,112,120530,112,120544,112,120588,112,120602,112,120646,112,120660,112,120704,112,120718,112,120762,112,120776,112,11427,112,65328,80,8473,80,119823,80,119875,80,119927,80,119979,80,120031,80,120083,80,120187,80,120239,80,120291,80,120343,80,120395,80,120447,80,929,80,120504,80,120562,80,120620,80,120678,80,120736,80,11426,80,5090,80,5229,80,42193,80,66197,80,119850,113,119902,113,119954,113,120006,113,120058,113,120110,113,120162,113,120214,113,120266,113,120318,113,120370,113,120422,113,120474,113,1307,113,1379,113,1382,113,8474,81,119824,81,119876,81,119928,81,119980,81,120032,81,120084,81,120188,81,120240,81,120292,81,120344,81,120396,81,120448,81,11605,81,119851,114,119903,114,119955,114,120007,114,120059,114,120111,114,120163,114,120215,114,120267,114,120319,114,120371,114,120423,114,120475,114,43847,114,43848,114,7462,114,11397,114,43905,114,119318,82,8475,82,8476,82,8477,82,119825,82,119877,82,119929,82,120033,82,120189,82,120241,82,120293,82,120345,82,120397,82,120449,82,422,82,5025,82,5074,82,66740,82,5511,82,42211,82,94005,82,65363,115,119852,115,119904,115,119956,115,120008,115,120060,115,120112,115,120164,115,120216,115,120268,115,120320,115,120372,115,120424,115,120476,115,42801,115,445,115,1109,115,43946,115,71873,115,66632,115,65331,83,119826,83,119878,83,119930,83,119982,83,120034,83,120086,83,120138,83,120190,83,120242,83,120294,83,120346,83,120398,83,120450,83,1029,83,1359,83,5077,83,5082,83,42210,83,94010,83,66198,83,66592,83,119853,116,119905,116,119957,116,120009,116,120061,116,120113,116,120165,116,120217,116,120269,116,120321,116,120373,116,120425,116,120477,116,8868,84,10201,84,128872,84,65332,84,119827,84,119879,84,119931,84,119983,84,120035,84,120087,84,120139,84,120191,84,120243,84,120295,84,120347,84,120399,84,120451,84,932,84,120507,84,120565,84,120623,84,120681,84,120739,84,11430,84,5026,84,42196,84,93962,84,71868,84,66199,84,66225,84,66325,84,119854,117,119906,117,119958,117,120010,117,120062,117,120114,117,120166,117,120218,117,120270,117,120322,117,120374,117,120426,117,120478,117,42911,117,7452,117,43854,117,43858,117,651,117,965,117,120534,117,120592,117,120650,117,120708,117,120766,117,1405,117,66806,117,71896,117,8746,85,8899,85,119828,85,119880,85,119932,85,119984,85,120036,85,120088,85,120140,85,120192,85,120244,85,120296,85,120348,85,120400,85,120452,85,1357,85,4608,85,66766,85,5196,85,42228,85,94018,85,71864,85,8744,118,8897,118,65366,118,8564,118,119855,118,119907,118,119959,118,120011,118,120063,118,120115,118,120167,118,120219,118,120271,118,120323,118,120375,118,120427,118,120479,118,7456,118,957,118,120526,118,120584,118,120642,118,120700,118,120758,118,1141,118,1496,118,71430,118,43945,118,71872,118,119309,86,1639,86,1783,86,8548,86,119829,86,119881,86,119933,86,119985,86,120037,86,120089,86,120141,86,120193,86,120245,86,120297,86,120349,86,120401,86,120453,86,1140,86,11576,86,5081,86,5167,86,42719,86,42214,86,93960,86,71840,86,66845,86,623,119,119856,119,119908,119,119960,119,120012,119,120064,119,120116,119,120168,119,120220,119,120272,119,120324,119,120376,119,120428,119,120480,119,7457,119,1121,119,1309,119,1377,119,71434,119,71438,119,71439,119,43907,119,71919,87,71910,87,119830,87,119882,87,119934,87,119986,87,120038,87,120090,87,120142,87,120194,87,120246,87,120298,87,120350,87,120402,87,120454,87,1308,87,5043,87,5076,87,42218,87,5742,120,10539,120,10540,120,10799,120,65368,120,8569,120,119857,120,119909,120,119961,120,120013,120,120065,120,120117,120,120169,120,120221,120,120273,120,120325,120,120377,120,120429,120,120481,120,5441,120,5501,120,5741,88,9587,88,66338,88,71916,88,65336,88,8553,88,119831,88,119883,88,119935,88,119987,88,120039,88,120091,88,120143,88,120195,88,120247,88,120299,88,120351,88,120403,88,120455,88,42931,88,935,88,120510,88,120568,88,120626,88,120684,88,120742,88,11436,88,11613,88,5815,88,42219,88,66192,88,66228,88,66327,88,66855,88,611,121,7564,121,65369,121,119858,121,119910,121,119962,121,120014,121,120066,121,120118,121,120170,121,120222,121,120274,121,120326,121,120378,121,120430,121,120482,121,655,121,7935,121,43866,121,947,121,8509,121,120516,121,120574,121,120632,121,120690,121,120748,121,1199,121,4327,121,71900,121,65337,89,119832,89,119884,89,119936,89,119988,89,120040,89,120092,89,120144,89,120196,89,120248,89,120300,89,120352,89,120404,89,120456,89,933,89,978,89,120508,89,120566,89,120624,89,120682,89,120740,89,11432,89,1198,89,5033,89,5053,89,42220,89,94019,89,71844,89,66226,89,119859,122,119911,122,119963,122,120015,122,120067,122,120119,122,120171,122,120223,122,120275,122,120327,122,120379,122,120431,122,120483,122,7458,122,43923,122,71876,122,66293,90,71909,90,65338,90,8484,90,8488,90,119833,90,119885,90,119937,90,119989,90,120041,90,120197,90,120249,90,120301,90,120353,90,120405,90,120457,90,918,90,120493,90,120551,90,120609,90,120667,90,120725,90,5059,90,42204,90,71849,90,65282,34,65284,36,65285,37,65286,38,65290,42,65291,43,65294,46,65295,47,65296,48,65297,49,65298,50,65299,51,65300,52,65301,53,65302,54,65303,55,65304,56,65305,57,65308,60,65309,61,65310,62,65312,64,65316,68,65318,70,65319,71,65324,76,65329,81,65330,82,65333,85,65334,86,65335,87,65343,95,65346,98,65348,100,65350,102,65355,107,65357,109,65358,110,65361,113,65362,114,65364,116,65365,117,65367,119,65370,122,65371,123,65373,125,119846,109],"_default":[160,32,8211,45,65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"cs":[65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"de":[65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"es":[8211,45,65374,126,65306,58,65281,33,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"fr":[65374,126,65306,58,65281,33,8216,96,8245,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"it":[160,32,8211,45,65374,126,65306,58,65281,33,8216,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"ja":[8211,45,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65292,44,65307,59],"ko":[8211,45,65374,126,65306,58,65281,33,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"pl":[65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"pt-BR":[65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"qps-ploc":[160,32,8211,45,65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"ru":[65374,126,65306,58,65281,33,8216,96,8217,96,8245,96,180,96,12494,47,305,105,921,73,1009,112,215,120,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"tr":[160,32,8211,45,65374,126,65306,58,65281,33,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65288,40,65289,41,65292,44,65307,59,65311,63],"zh-hans":[65374,126,65306,58,65281,33,8245,96,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65288,40,65289,41],"zh-hant":[8211,45,65374,126,180,96,12494,47,1047,51,1073,54,1072,97,1040,65,1068,98,1042,66,1089,99,1057,67,1077,101,1045,69,1053,72,305,105,1050,75,921,73,1052,77,1086,111,1054,79,1009,112,1088,112,1056,80,1075,114,1058,84,215,120,1093,120,1061,88,1091,121,1059,89,65283,35,65307,59]}')), oe.cache = new M.LRUCachedFunction(k => {
      function U(ue) {
        const fe = new Map;
        for (let ge = 0; ge < ue.length; ge += 2) fe.set(ue[ge], ue[ge + 1]);
        return fe;
      }

      function V(ue, fe) {
        const ge = new Map(ue);
        for (const [ve, Ne] of fe) ge.set(ve, Ne);
        return ge;
      }

      function $(ue, fe) {
        if (!ue) return fe;
        const ge = new Map;
        for (const [ve, Ne] of ue) fe.has(ve) && ge.set(ve, Ne);
        return ge;
      }

      const ee = i.ambiguousCharacterData.value;
      let ae = k.filter(ue => !ue.startsWith('_') && ue in ee);
      ae.length === 0 && (ae = ['_default']);
      let he;
      for (const ue of ae) {
        const fe = U(ee[ue]);
        he = $(he, fe);
      }
      const me = U(ee._common),
        de = V(me, he);
      return new i(de);
    }), oe._locales = new R.Lazy(() => Object.keys(i.ambiguousCharacterData.value)
      .filter(k => !k.startsWith('_')));

    class le {
      static get codePoints() {
        return le.getData();
      }

      static getRawData() {
        return JSON.parse('[9,10,11,12,13,32,127,160,173,847,1564,4447,4448,6068,6069,6155,6156,6157,6158,7355,7356,8192,8193,8194,8195,8196,8197,8198,8199,8200,8201,8202,8203,8204,8205,8206,8207,8234,8235,8236,8237,8238,8239,8287,8288,8289,8290,8291,8292,8293,8294,8295,8296,8297,8298,8299,8300,8301,8302,8303,10240,12288,12644,65024,65025,65026,65027,65028,65029,65030,65031,65032,65033,65034,65035,65036,65037,65038,65039,65279,65440,65520,65521,65522,65523,65524,65525,65526,65527,65528,65532,78844,119155,119156,119157,119158,119159,119160,119161,119162,917504,917505,917506,917507,917508,917509,917510,917511,917512,917513,917514,917515,917516,917517,917518,917519,917520,917521,917522,917523,917524,917525,917526,917527,917528,917529,917530,917531,917532,917533,917534,917535,917536,917537,917538,917539,917540,917541,917542,917543,917544,917545,917546,917547,917548,917549,917550,917551,917552,917553,917554,917555,917556,917557,917558,917559,917560,917561,917562,917563,917564,917565,917566,917567,917568,917569,917570,917571,917572,917573,917574,917575,917576,917577,917578,917579,917580,917581,917582,917583,917584,917585,917586,917587,917588,917589,917590,917591,917592,917593,917594,917595,917596,917597,917598,917599,917600,917601,917602,917603,917604,917605,917606,917607,917608,917609,917610,917611,917612,917613,917614,917615,917616,917617,917618,917619,917620,917621,917622,917623,917624,917625,917626,917627,917628,917629,917630,917631,917760,917761,917762,917763,917764,917765,917766,917767,917768,917769,917770,917771,917772,917773,917774,917775,917776,917777,917778,917779,917780,917781,917782,917783,917784,917785,917786,917787,917788,917789,917790,917791,917792,917793,917794,917795,917796,917797,917798,917799,917800,917801,917802,917803,917804,917805,917806,917807,917808,917809,917810,917811,917812,917813,917814,917815,917816,917817,917818,917819,917820,917821,917822,917823,917824,917825,917826,917827,917828,917829,917830,917831,917832,917833,917834,917835,917836,917837,917838,917839,917840,917841,917842,917843,917844,917845,917846,917847,917848,917849,917850,917851,917852,917853,917854,917855,917856,917857,917858,917859,917860,917861,917862,917863,917864,917865,917866,917867,917868,917869,917870,917871,917872,917873,917874,917875,917876,917877,917878,917879,917880,917881,917882,917883,917884,917885,917886,917887,917888,917889,917890,917891,917892,917893,917894,917895,917896,917897,917898,917899,917900,917901,917902,917903,917904,917905,917906,917907,917908,917909,917910,917911,917912,917913,917914,917915,917916,917917,917918,917919,917920,917921,917922,917923,917924,917925,917926,917927,917928,917929,917930,917931,917932,917933,917934,917935,917936,917937,917938,917939,917940,917941,917942,917943,917944,917945,917946,917947,917948,917949,917950,917951,917952,917953,917954,917955,917956,917957,917958,917959,917960,917961,917962,917963,917964,917965,917966,917967,917968,917969,917970,917971,917972,917973,917974,917975,917976,917977,917978,917979,917980,917981,917982,917983,917984,917985,917986,917987,917988,917989,917990,917991,917992,917993,917994,917995,917996,917997,917998,917999]');
      }

      static getData() {
        return this._data || (this._data = new Set(le.getRawData())), this._data;
      }

      static isInvisibleCharacter(U) {
        return le.getData()
          .has(U);
      }
    }

    t.InvisibleCharacters = le, le._data = void 0;
  }), X(J[39], Z([0, 1, 6]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.StringSHA1 = t.toHexString = t.stringHash = t.numberHash = t.doHash = t.hash = void 0;

    function R(b) {
      return i(b, 0);
    }

    t.hash = R;

    function i(b, f) {
      switch (typeof b) {
        case'object':
          return b === null ? d(349, f) : Array.isArray(b) ? c(b, f) : o(b, f);
        case'string':
          return p(b, f);
        case'boolean':
          return _(b, f);
        case'number':
          return d(b, f);
        case'undefined':
          return d(937, f);
        default:
          return d(617, f);
      }
    }

    t.doHash = i;

    function d(b, f) {
      return (f << 5) - f + b | 0;
    }

    t.numberHash = d;

    function _(b, f) {
      return d(b ? 433 : 863, f);
    }

    function p(b, f) {
      f = d(149417, f);
      for (let y = 0, w = b.length; y < w; y++) f = d(b.charCodeAt(y), f);
      return f;
    }

    t.stringHash = p;

    function c(b, f) {
      return f = d(104579, f), b.reduce((y, w) => i(w, y), f);
    }

    function o(b, f) {
      return f = d(181387, f), Object.keys(b)
        .sort()
        .reduce((y, w) => (y = p(w, y), i(b[w], y)), f);
    }

    function L(b, f, y = 32) {
      const w = y - f,
        E = ~((1 << w) - 1);
      return (b << f | (E & b) >>> w) >>> 0;
    }

    function e(b, f = 0, y = b.byteLength, w = 0) {
      for (let E = 0; E < y; E++) b[f + E] = w;
    }

    function s(b, f, y = '0') {
      for (; b.length < f;) b = y + b;
      return b;
    }

    function l(b, f = 32) {
      return b instanceof ArrayBuffer ? Array.from(new Uint8Array(b))
        .map(y => y.toString(16)
          .padStart(2, '0'))
        .join('') : s((b >>> 0).toString(16), f / 4);
    }

    t.toHexString = l;

    class u {
      constructor() {
        this._h0 = 1732584193, this._h1 = 4023233417, this._h2 = 2562383102, this._h3 = 271733878, this._h4 = 3285377520, this._buff = new Uint8Array(67), this._buffDV = new DataView(this._buff.buffer), this._buffLen = 0, this._totalLen = 0, this._leftoverHighSurrogate = 0, this._finished = !1;
      }

      update(f) {
        const y = f.length;
        if (y === 0) return;
        const w = this._buff;
        let E = this._buffLen,
          S = this._leftoverHighSurrogate,
          C,
          r;
        for (S !== 0 ? (C = S, r = -1, S = 0) : (C = f.charCodeAt(0), r = 0); ;) {
          let a = C;
          if (M.isHighSurrogate(C)) {
            if (r + 1 < y) {
              const g = f.charCodeAt(r + 1);
              M.isLowSurrogate(g) ? (r++, a = M.computeCodePoint(C, g)) : a = 65533;
            } else {
              S = C;
              break;
            }
          } else {
            M.isLowSurrogate(C) && (a = 65533);
          }
          if (E = this._push(w, E, a), r++, r < y) C = f.charCodeAt(r); else break;
        }
        this._buffLen = E, this._leftoverHighSurrogate = S;
      }

      _push(f, y, w) {
        return w < 128 ? f[y++] = w : w < 2048 ? (f[y++] = 192 | (w & 1984) >>> 6, f[y++] = 128 | (w & 63) >>> 0) : w < 65536 ? (f[y++] = 224 | (w & 61440) >>> 12, f[y++] = 128 | (w & 4032) >>> 6, f[y++] = 128 | (w & 63) >>> 0) : (f[y++] = 240 | (w & 1835008) >>> 18, f[y++] = 128 | (w & 258048) >>> 12, f[y++] = 128 | (w & 4032) >>> 6, f[y++] = 128 | (w & 63) >>> 0), y >= 64 && (this._step(), y -= 64, this._totalLen += 64, f[0] = f[64], f[1] = f[65], f[2] = f[66]), y;
      }

      digest() {
        return this._finished || (this._finished = !0, this._leftoverHighSurrogate && (this._leftoverHighSurrogate = 0, this._buffLen = this._push(this._buff, this._buffLen, 65533)), this._totalLen += this._buffLen, this._wrapUp()), l(this._h0) + l(this._h1) + l(this._h2) + l(this._h3) + l(this._h4);
      }

      _wrapUp() {
        this._buff[this._buffLen++] = 128, e(this._buff, this._buffLen), this._buffLen > 56 && (this._step(), e(this._buff));
        const f = 8 * this._totalLen;
        this._buffDV.setUint32(56, Math.floor(f / 4294967296), !1), this._buffDV.setUint32(60, f % 4294967296, !1), this._step();
      }

      _step() {
        const f = u._bigBlock32,
          y = this._buffDV;
        for (let h = 0; h < 64; h += 4) f.setUint32(h, y.getUint32(h, !1), !1);
        for (let h = 64; h < 320; h += 4) f.setUint32(h, L(f.getUint32(h - 12, !1) ^ f.getUint32(h - 32, !1) ^ f.getUint32(h - 56, !1) ^ f.getUint32(h - 64, !1), 1), !1);
        let w = this._h0,
          E = this._h1,
          S = this._h2,
          C = this._h3,
          r = this._h4,
          a,
          g,
          m;
        for (let h = 0; h < 80; h++) h < 20 ? (a = E & S | ~E & C, g = 1518500249) : h < 40 ? (a = E ^ S ^ C, g = 1859775393) : h < 60 ? (a = E & S | E & C | S & C, g = 2400959708) : (a = E ^ S ^ C, g = 3395469782), m = L(w, 5) + a + r + g + f.getUint32(h * 4, !1) & 4294967295, r = C, C = S, S = L(E, 30), E = w, w = m;
        this._h0 = this._h0 + w & 4294967295, this._h1 = this._h1 + E & 4294967295, this._h2 = this._h2 + S & 4294967295, this._h3 = this._h3 + C & 4294967295, this._h4 = this._h4 + r & 4294967295;
      }
    }

    t.StringSHA1 = u, u._bigBlock32 = new DataView(new ArrayBuffer(320));
  }), X(J[24], Z([0, 1, 34, 39]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LcsDiff = t.stringDiff = t.StringDiffSequence = void 0;

    class i {
      constructor(e) {
        this.source = e;
      }

      getElements() {
        const e = this.source,
          s = new Int32Array(e.length);
        for (let l = 0, u = e.length; l < u; l++) s[l] = e.charCodeAt(l);
        return s;
      }
    }

    t.StringDiffSequence = i;

    function d(L, e, s) {
      return new o(new i(L), new i(e)).ComputeDiff(s).changes;
    }

    t.stringDiff = d;

    class _ {
      static Assert(e, s) {
        if (!e) throw new Error(s);
      }
    }

    class p {
      static Copy(e, s, l, u, b) {
        for (let f = 0; f < b; f++) l[u + f] = e[s + f];
      }

      static Copy2(e, s, l, u, b) {
        for (let f = 0; f < b; f++) l[u + f] = e[s + f];
      }
    }

    class c {
      constructor() {
        this.m_changes = [], this.m_originalStart = 1073741824, this.m_modifiedStart = 1073741824, this.m_originalCount = 0, this.m_modifiedCount = 0;
      }

      MarkNextChange() {
        (this.m_originalCount > 0 || this.m_modifiedCount > 0) && this.m_changes.push(new M.DiffChange(this.m_originalStart, this.m_originalCount, this.m_modifiedStart, this.m_modifiedCount)), this.m_originalCount = 0, this.m_modifiedCount = 0, this.m_originalStart = 1073741824, this.m_modifiedStart = 1073741824;
      }

      AddOriginalElement(e, s) {
        this.m_originalStart = Math.min(this.m_originalStart, e), this.m_modifiedStart = Math.min(this.m_modifiedStart, s), this.m_originalCount++;
      }

      AddModifiedElement(e, s) {
        this.m_originalStart = Math.min(this.m_originalStart, e), this.m_modifiedStart = Math.min(this.m_modifiedStart, s), this.m_modifiedCount++;
      }

      getChanges() {
        return (this.m_originalCount > 0 || this.m_modifiedCount > 0) && this.MarkNextChange(), this.m_changes;
      }

      getReverseChanges() {
        return (this.m_originalCount > 0 || this.m_modifiedCount > 0) && this.MarkNextChange(), this.m_changes.reverse(), this.m_changes;
      }
    }

    class o {
      constructor(e, s, l = null) {
        this.ContinueProcessingPredicate = l, this._originalSequence = e, this._modifiedSequence = s;
        const [u, b, f] = o._getElements(e), [y, w, E] = o._getElements(s);
        this._hasStrings = f && E, this._originalStringElements = u, this._originalElementsOrHash = b, this._modifiedStringElements = y, this._modifiedElementsOrHash = w, this.m_forwardHistory = [], this.m_reverseHistory = [];
      }

      static _isStringArray(e) {
        return e.length > 0 && typeof e[0] == 'string';
      }

      static _getElements(e) {
        const s = e.getElements();
        if (o._isStringArray(s)) {
          const l = new Int32Array(s.length);
          for (let u = 0, b = s.length; u < b; u++) l[u] = (0, R.stringHash)(s[u], 0);
          return [s, l, !0];
        }
        return s instanceof Int32Array ? [[], s, !1] : [[], new Int32Array(s), !1];
      }

      static _getStrictElement(e, s) {
        return typeof e.getStrictElement == 'function' ? e.getStrictElement(s) : null;
      }

      ElementsAreEqual(e, s) {
        return this._originalElementsOrHash[e] !== this._modifiedElementsOrHash[s] ? !1 : this._hasStrings ? this._originalStringElements[e] === this._modifiedStringElements[s] : !0;
      }

      ElementsAreStrictEqual(e, s) {
        if (!this.ElementsAreEqual(e, s)) return !1;
        const l = o._getStrictElement(this._originalSequence, e),
          u = o._getStrictElement(this._modifiedSequence, s);
        return l === u;
      }

      OriginalElementsAreEqual(e, s) {
        return this._originalElementsOrHash[e] !== this._originalElementsOrHash[s] ? !1 : this._hasStrings ? this._originalStringElements[e] === this._originalStringElements[s] : !0;
      }

      ModifiedElementsAreEqual(e, s) {
        return this._modifiedElementsOrHash[e] !== this._modifiedElementsOrHash[s] ? !1 : this._hasStrings ? this._modifiedStringElements[e] === this._modifiedStringElements[s] : !0;
      }

      ComputeDiff(e) {
        return this._ComputeDiff(0, this._originalElementsOrHash.length - 1, 0, this._modifiedElementsOrHash.length - 1, e);
      }

      _ComputeDiff(e, s, l, u, b) {
        const f = [!1];
        let y = this.ComputeDiffRecursive(e, s, l, u, f);
        return b && (y = this.PrettifyChanges(y)), {
          quitEarly: f[0],
          changes: y,
        };
      }

      ComputeDiffRecursive(e, s, l, u, b) {
        for (b[0] = !1; e <= s && l <= u && this.ElementsAreEqual(e, l);) e++, l++;
        for (; s >= e && u >= l && this.ElementsAreEqual(s, u);) s--, u--;
        if (e > s || l > u) {
          let C;
          return l <= u ? (_.Assert(e === s + 1, 'originalStart should only be one more than originalEnd'), C = [new M.DiffChange(e, 0, l, u - l + 1)]) : e <= s ? (_.Assert(l === u + 1, 'modifiedStart should only be one more than modifiedEnd'), C = [new M.DiffChange(e, s - e + 1, l, 0)]) : (_.Assert(e === s + 1, 'originalStart should only be one more than originalEnd'), _.Assert(l === u + 1, 'modifiedStart should only be one more than modifiedEnd'), C = []), C;
        }
        const f = [0],
          y = [0],
          w = this.ComputeRecursionPoint(e, s, l, u, f, y, b),
          E = f[0],
          S = y[0];
        if (w !== null) return w;
        if (!b[0]) {
          const C = this.ComputeDiffRecursive(e, E, l, S, b);
          let r = [];
          return b[0] ? r = [new M.DiffChange(E + 1, s - (E + 1) + 1, S + 1, u - (S + 1) + 1)] : r = this.ComputeDiffRecursive(E + 1, s, S + 1, u, b), this.ConcatenateChanges(C, r);
        }
        return [new M.DiffChange(e, s - e + 1, l, u - l + 1)];
      }

      WALKTRACE(e, s, l, u, b, f, y, w, E, S, C, r, a, g, m, h, v, N) {
        let A = null,
          D = null,
          P = new c,
          T = s,
          I = l,
          B = a[0] - h[0] - u,
          z = -1073741824,
          x = this.m_forwardHistory.length - 1;
        do {
          const O = B + e;
          O === T || O < I && E[O - 1] < E[O + 1] ? (C = E[O + 1], g = C - B - u, C < z && P.MarkNextChange(), z = C, P.AddModifiedElement(C + 1, g), B = O + 1 - e) : (C = E[O - 1] + 1, g = C - B - u, C < z && P.MarkNextChange(), z = C - 1, P.AddOriginalElement(C, g + 1), B = O - 1 - e), x >= 0 && (E = this.m_forwardHistory[x], e = E[0], T = 1, I = E.length - 1);
        } while (--x >= -1);
        if (A = P.getReverseChanges(), N[0]) {
          let O = a[0] + 1,
            F = h[0] + 1;
          if (A !== null && A.length > 0) {
            const W = A[A.length - 1];
            O = Math.max(O, W.getOriginalEnd()), F = Math.max(F, W.getModifiedEnd());
          }
          D = [new M.DiffChange(O, r - O + 1, F, m - F + 1)];
        } else {
          P = new c, T = f, I = y, B = a[0] - h[0] - w, z = 1073741824, x = v ? this.m_reverseHistory.length - 1 : this.m_reverseHistory.length - 2;
          do {
            const O = B + b;
            O === T || O < I && S[O - 1] >= S[O + 1] ? (C = S[O + 1] - 1, g = C - B - w, C > z && P.MarkNextChange(), z = C + 1, P.AddOriginalElement(C + 1, g + 1), B = O + 1 - b) : (C = S[O - 1], g = C - B - w, C > z && P.MarkNextChange(), z = C, P.AddModifiedElement(C + 1, g + 1), B = O - 1 - b), x >= 0 && (S = this.m_reverseHistory[x], b = S[0], T = 1, I = S.length - 1);
          } while (--x >= -1);
          D = P.getChanges();
        }
        return this.ConcatenateChanges(A, D);
      }

      ComputeRecursionPoint(e, s, l, u, b, f, y) {
        let w = 0,
          E = 0,
          S = 0,
          C = 0,
          r = 0,
          a = 0;
        e--, l--, b[0] = 0, f[0] = 0, this.m_forwardHistory = [], this.m_reverseHistory = [];
        const g = s - e + (u - l),
          m = g + 1,
          h = new Int32Array(m),
          v = new Int32Array(m),
          N = u - l,
          A = s - e,
          D = e - l,
          P = s - u,
          I = (A - N) % 2 === 0;
        h[N] = e, v[A] = s, y[0] = !1;
        for (let B = 1; B <= g / 2 + 1; B++) {
          let z = 0,
            x = 0;
          S = this.ClipDiagonalBound(N - B, B, N, m), C = this.ClipDiagonalBound(N + B, B, N, m);
          for (let F = S; F <= C; F += 2) {
            F === S || F < C && h[F - 1] < h[F + 1] ? w = h[F + 1] : w = h[F - 1] + 1, E = w - (F - N) - D;
            const W = w;
            for (; w < s && E < u && this.ElementsAreEqual(w + 1, E + 1);) w++, E++;
            if (h[F] = w, w + E > z + x && (z = w, x = E), !I && Math.abs(F - A) <= B - 1 && w >= v[F]) return b[0] = w, f[0] = E, W <= v[F] && 1447 > 0 && B <= 1448 ? this.WALKTRACE(N, S, C, D, A, r, a, P, h, v, w, s, b, E, u, f, I, y) : null;
          }
          const O = (z - e + (x - l) - B) / 2;
          if (this.ContinueProcessingPredicate !== null && !this.ContinueProcessingPredicate(z, O)) return y[0] = !0, b[0] = z, f[0] = x, O > 0 && 1447 > 0 && B <= 1448 ? this.WALKTRACE(N, S, C, D, A, r, a, P, h, v, w, s, b, E, u, f, I, y) : (e++, l++, [new M.DiffChange(e, s - e + 1, l, u - l + 1)]);
          r = this.ClipDiagonalBound(A - B, B, A, m), a = this.ClipDiagonalBound(A + B, B, A, m);
          for (let F = r; F <= a; F += 2) {
            F === r || F < a && v[F - 1] >= v[F + 1] ? w = v[F + 1] - 1 : w = v[F - 1], E = w - (F - A) - P;
            const W = w;
            for (; w > e && E > l && this.ElementsAreEqual(w, E);) w--, E--;
            if (v[F] = w, I && Math.abs(F - N) <= B && w <= h[F]) return b[0] = w, f[0] = E, W >= h[F] && 1447 > 0 && B <= 1448 ? this.WALKTRACE(N, S, C, D, A, r, a, P, h, v, w, s, b, E, u, f, I, y) : null;
          }
          if (B <= 1447) {
            let F = new Int32Array(C - S + 2);
            F[0] = N - S + 1, p.Copy2(h, S, F, 1, C - S + 1), this.m_forwardHistory.push(F), F = new Int32Array(a - r + 2), F[0] = A - r + 1, p.Copy2(v, r, F, 1, a - r + 1), this.m_reverseHistory.push(F);
          }
        }
        return this.WALKTRACE(N, S, C, D, A, r, a, P, h, v, w, s, b, E, u, f, I, y);
      }

      PrettifyChanges(e) {
        for (let s = 0; s < e.length; s++) {
          const l = e[s],
            u = s < e.length - 1 ? e[s + 1].originalStart : this._originalElementsOrHash.length,
            b = s < e.length - 1 ? e[s + 1].modifiedStart : this._modifiedElementsOrHash.length,
            f = l.originalLength > 0,
            y = l.modifiedLength > 0;
          for (; l.originalStart + l.originalLength < u && l.modifiedStart + l.modifiedLength < b && (!f || this.OriginalElementsAreEqual(l.originalStart, l.originalStart + l.originalLength)) && (!y || this.ModifiedElementsAreEqual(l.modifiedStart, l.modifiedStart + l.modifiedLength));) {
            const E = this.ElementsAreStrictEqual(l.originalStart, l.modifiedStart);
            if (this.ElementsAreStrictEqual(l.originalStart + l.originalLength, l.modifiedStart + l.modifiedLength) && !E) break;
            l.originalStart++, l.modifiedStart++;
          }
          const w = [null];
          if (s < e.length - 1 && this.ChangesOverlap(e[s], e[s + 1], w)) {
            e[s] = w[0], e.splice(s + 1, 1), s--;
            continue;
          }
        }
        for (let s = e.length - 1; s >= 0; s--) {
          const l = e[s];
          let u = 0,
            b = 0;
          if (s > 0) {
            const C = e[s - 1];
            u = C.originalStart + C.originalLength, b = C.modifiedStart + C.modifiedLength;
          }
          const f = l.originalLength > 0,
            y = l.modifiedLength > 0;
          let w = 0,
            E = this._boundaryScore(l.originalStart, l.originalLength, l.modifiedStart, l.modifiedLength);
          for (let C = 1; ; C++) {
            const r = l.originalStart - C,
              a = l.modifiedStart - C;
            if (r < u || a < b || f && !this.OriginalElementsAreEqual(r, r + l.originalLength) || y && !this.ModifiedElementsAreEqual(a, a + l.modifiedLength)) break;
            const m = (r === u && a === b ? 5 : 0) + this._boundaryScore(r, l.originalLength, a, l.modifiedLength);
            m > E && (E = m, w = C);
          }
          l.originalStart -= w, l.modifiedStart -= w;
          const S = [null];
          if (s > 0 && this.ChangesOverlap(e[s - 1], e[s], S)) {
            e[s - 1] = S[0], e.splice(s, 1), s++;
            continue;
          }
        }
        if (this._hasStrings) {
          for (let s = 1, l = e.length; s < l; s++) {
            const u = e[s - 1],
              b = e[s],
              f = b.originalStart - u.originalStart - u.originalLength,
              y = u.originalStart,
              w = b.originalStart + b.originalLength,
              E = w - y,
              S = u.modifiedStart,
              C = b.modifiedStart + b.modifiedLength,
              r = C - S;
            if (f < 5 && E < 20 && r < 20) {
              const a = this._findBetterContiguousSequence(y, E, S, r, f);
              if (a) {
                const [g, m] = a;
                (g !== u.originalStart + u.originalLength || m !== u.modifiedStart + u.modifiedLength) && (u.originalLength = g - u.originalStart, u.modifiedLength = m - u.modifiedStart, b.originalStart = g + f, b.modifiedStart = m + f, b.originalLength = w - b.originalStart, b.modifiedLength = C - b.modifiedStart);
              }
            }
          }
        }
        return e;
      }

      _findBetterContiguousSequence(e, s, l, u, b) {
        if (s < b || u < b) return null;
        const f = e + s - b + 1,
          y = l + u - b + 1;
        let w = 0,
          E = 0,
          S = 0;
        for (let C = e; C < f; C++) {
          for (let r = l; r < y; r++) {
            const a = this._contiguousSequenceScore(C, r, b);
            a > 0 && a > w && (w = a, E = C, S = r);
          }
        }
        return w > 0 ? [E, S] : null;
      }

      _contiguousSequenceScore(e, s, l) {
        let u = 0;
        for (let b = 0; b < l; b++) {
          if (!this.ElementsAreEqual(e + b, s + b)) return 0;
          u += this._originalStringElements[e + b].length;
        }
        return u;
      }

      _OriginalIsBoundary(e) {
        return e <= 0 || e >= this._originalElementsOrHash.length - 1 ? !0 : this._hasStrings && /^\s*$/.test(this._originalStringElements[e]);
      }

      _OriginalRegionIsBoundary(e, s) {
        if (this._OriginalIsBoundary(e) || this._OriginalIsBoundary(e - 1)) return !0;
        if (s > 0) {
          const l = e + s;
          if (this._OriginalIsBoundary(l - 1) || this._OriginalIsBoundary(l)) return !0;
        }
        return !1;
      }

      _ModifiedIsBoundary(e) {
        return e <= 0 || e >= this._modifiedElementsOrHash.length - 1 ? !0 : this._hasStrings && /^\s*$/.test(this._modifiedStringElements[e]);
      }

      _ModifiedRegionIsBoundary(e, s) {
        if (this._ModifiedIsBoundary(e) || this._ModifiedIsBoundary(e - 1)) return !0;
        if (s > 0) {
          const l = e + s;
          if (this._ModifiedIsBoundary(l - 1) || this._ModifiedIsBoundary(l)) return !0;
        }
        return !1;
      }

      _boundaryScore(e, s, l, u) {
        const b = this._OriginalRegionIsBoundary(e, s) ? 1 : 0,
          f = this._ModifiedRegionIsBoundary(l, u) ? 1 : 0;
        return b + f;
      }

      ConcatenateChanges(e, s) {
        const l = [];
        if (e.length === 0 || s.length === 0) return s.length > 0 ? s : e;
        if (this.ChangesOverlap(e[e.length - 1], s[0], l)) {
          const u = new Array(e.length + s.length - 1);
          return p.Copy(e, 0, u, 0, e.length - 1), u[e.length - 1] = l[0], p.Copy(s, 1, u, e.length, s.length - 1), u;
        } else {
          const u = new Array(e.length + s.length);
          return p.Copy(e, 0, u, 0, e.length), p.Copy(s, 0, u, e.length, s.length), u;
        }
      }

      ChangesOverlap(e, s, l) {
        if (_.Assert(e.originalStart <= s.originalStart, 'Left change is not less than or equal to right change'), _.Assert(e.modifiedStart <= s.modifiedStart, 'Left change is not less than or equal to right change'), e.originalStart + e.originalLength >= s.originalStart || e.modifiedStart + e.modifiedLength >= s.modifiedStart) {
          const u = e.originalStart;
          let b = e.originalLength;
          const f = e.modifiedStart;
          let y = e.modifiedLength;
          return e.originalStart + e.originalLength >= s.originalStart && (b = s.originalStart + s.originalLength - e.originalStart), e.modifiedStart + e.modifiedLength >= s.modifiedStart && (y = s.modifiedStart + s.modifiedLength - e.modifiedStart), l[0] = new M.DiffChange(u, b, f, y), !0;
        } else {
          return l[0] = null, !1;
        }
      }

      ClipDiagonalBound(e, s, l, u) {
        if (e >= 0 && e < u) return e;
        const b = l,
          f = u - l - 1,
          y = s % 2 === 0;
        if (e < 0) {
          const w = b % 2 === 0;
          return y === w ? 0 : 1;
        } else {
          const w = f % 2 === 0;
          return y === w ? u - 1 : u - 2;
        }
      }
    }

    t.LcsDiff = o;
  }), X(J[25], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.validateConstraint = t.validateConstraints = t.isFunction = t.assertIsDefined = t.assertType = t.isUndefinedOrNull = t.isDefined = t.isUndefined = t.isBoolean = t.isIterable = t.isNumber = t.isTypedArray = t.isObject = t.isString = void 0;

    function M(f) {
      return typeof f == 'string';
    }

    t.isString = M;

    function R(f) {
      return typeof f == 'object' && f !== null && !Array.isArray(f) && !(f instanceof RegExp) && !(f instanceof Date);
    }

    t.isObject = R;

    function i(f) {
      const y = Object.getPrototypeOf(Uint8Array);
      return typeof f == 'object' && f instanceof y;
    }

    t.isTypedArray = i;

    function d(f) {
      return typeof f == 'number' && !isNaN(f);
    }

    t.isNumber = d;

    function _(f) {
      return !!f && typeof f[Symbol.iterator] == 'function';
    }

    t.isIterable = _;

    function p(f) {
      return f === !0 || f === !1;
    }

    t.isBoolean = p;

    function c(f) {
      return typeof f > 'u';
    }

    t.isUndefined = c;

    function o(f) {
      return !L(f);
    }

    t.isDefined = o;

    function L(f) {
      return c(f) || f === null;
    }

    t.isUndefinedOrNull = L;

    function e(f, y) {
      if (!f) throw new Error(y ? `Unexpected type, expected '${y}'` : 'Unexpected type');
    }

    t.assertType = e;

    function s(f) {
      if (L(f)) throw new Error('Assertion Failed: argument is undefined or null');
      return f;
    }

    t.assertIsDefined = s;

    function l(f) {
      return typeof f == 'function';
    }

    t.isFunction = l;

    function u(f, y) {
      const w = Math.min(f.length, y.length);
      for (let E = 0; E < w; E++) b(f[E], y[E]);
    }

    t.validateConstraints = u;

    function b(f, y) {
      if (M(y)) {
        if (typeof f !== y) throw new Error(`argument does not match constraint: typeof ${y}`);
      } else if (l(y)) {
        try {
          if (f instanceof y) return;
        } catch {
        }
        if (!L(f) && f.constructor === y || y.length === 1 && y.call(void 0, f) === !0) return;
        throw new Error('argument does not match one of these constraints: arg instanceof constraint, arg.constructor === constraint, nor constraint(arg) === true');
      }
    }

    t.validateConstraint = b;
  }), X(J[40], Z([0, 1, 25]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Codicon = t.getCodiconFontCharacters = void 0;
    const R = Object.create(null);

    function i(_, p) {
      if ((0, M.isString)(p)) {
        const c = R[p];
        if (c === void 0) throw new Error(`${_} references an unknown codicon: ${p}`);
        p = c;
      }
      return R[_] = p, { id: _ };
    }

    function d() {
      return R;
    }

    t.getCodiconFontCharacters = d, t.Codicon = {
      add: i('add', 6e4),
      plus: i('plus', 6e4),
      gistNew: i('gist-new', 6e4),
      repoCreate: i('repo-create', 6e4),
      lightbulb: i('lightbulb', 60001),
      lightBulb: i('light-bulb', 60001),
      repo: i('repo', 60002),
      repoDelete: i('repo-delete', 60002),
      gistFork: i('gist-fork', 60003),
      repoForked: i('repo-forked', 60003),
      gitPullRequest: i('git-pull-request', 60004),
      gitPullRequestAbandoned: i('git-pull-request-abandoned', 60004),
      recordKeys: i('record-keys', 60005),
      keyboard: i('keyboard', 60005),
      tag: i('tag', 60006),
      tagAdd: i('tag-add', 60006),
      tagRemove: i('tag-remove', 60006),
      gitPullRequestLabel: i('git-pull-request-label', 60006),
      person: i('person', 60007),
      personFollow: i('person-follow', 60007),
      personOutline: i('person-outline', 60007),
      personFilled: i('person-filled', 60007),
      gitBranch: i('git-branch', 60008),
      gitBranchCreate: i('git-branch-create', 60008),
      gitBranchDelete: i('git-branch-delete', 60008),
      sourceControl: i('source-control', 60008),
      mirror: i('mirror', 60009),
      mirrorPublic: i('mirror-public', 60009),
      star: i('star', 60010),
      starAdd: i('star-add', 60010),
      starDelete: i('star-delete', 60010),
      starEmpty: i('star-empty', 60010),
      comment: i('comment', 60011),
      commentAdd: i('comment-add', 60011),
      alert: i('alert', 60012),
      warning: i('warning', 60012),
      search: i('search', 60013),
      searchSave: i('search-save', 60013),
      logOut: i('log-out', 60014),
      signOut: i('sign-out', 60014),
      logIn: i('log-in', 60015),
      signIn: i('sign-in', 60015),
      eye: i('eye', 60016),
      eyeUnwatch: i('eye-unwatch', 60016),
      eyeWatch: i('eye-watch', 60016),
      circleFilled: i('circle-filled', 60017),
      primitiveDot: i('primitive-dot', 60017),
      closeDirty: i('close-dirty', 60017),
      debugBreakpoint: i('debug-breakpoint', 60017),
      debugBreakpointDisabled: i('debug-breakpoint-disabled', 60017),
      debugBreakpointPending: i('debug-breakpoint-pending', 60377),
      debugHint: i('debug-hint', 60017),
      primitiveSquare: i('primitive-square', 60018),
      edit: i('edit', 60019),
      pencil: i('pencil', 60019),
      info: i('info', 60020),
      issueOpened: i('issue-opened', 60020),
      gistPrivate: i('gist-private', 60021),
      gitForkPrivate: i('git-fork-private', 60021),
      lock: i('lock', 60021),
      mirrorPrivate: i('mirror-private', 60021),
      close: i('close', 60022),
      removeClose: i('remove-close', 60022),
      x: i('x', 60022),
      repoSync: i('repo-sync', 60023),
      sync: i('sync', 60023),
      clone: i('clone', 60024),
      desktopDownload: i('desktop-download', 60024),
      beaker: i('beaker', 60025),
      microscope: i('microscope', 60025),
      vm: i('vm', 60026),
      deviceDesktop: i('device-desktop', 60026),
      file: i('file', 60027),
      fileText: i('file-text', 60027),
      more: i('more', 60028),
      ellipsis: i('ellipsis', 60028),
      kebabHorizontal: i('kebab-horizontal', 60028),
      mailReply: i('mail-reply', 60029),
      reply: i('reply', 60029),
      organization: i('organization', 60030),
      organizationFilled: i('organization-filled', 60030),
      organizationOutline: i('organization-outline', 60030),
      newFile: i('new-file', 60031),
      fileAdd: i('file-add', 60031),
      newFolder: i('new-folder', 60032),
      fileDirectoryCreate: i('file-directory-create', 60032),
      trash: i('trash', 60033),
      trashcan: i('trashcan', 60033),
      history: i('history', 60034),
      clock: i('clock', 60034),
      folder: i('folder', 60035),
      fileDirectory: i('file-directory', 60035),
      symbolFolder: i('symbol-folder', 60035),
      logoGithub: i('logo-github', 60036),
      markGithub: i('mark-github', 60036),
      github: i('github', 60036),
      terminal: i('terminal', 60037),
      console: i('console', 60037),
      repl: i('repl', 60037),
      zap: i('zap', 60038),
      symbolEvent: i('symbol-event', 60038),
      error: i('error', 60039),
      stop: i('stop', 60039),
      variable: i('variable', 60040),
      symbolVariable: i('symbol-variable', 60040),
      array: i('array', 60042),
      symbolArray: i('symbol-array', 60042),
      symbolModule: i('symbol-module', 60043),
      symbolPackage: i('symbol-package', 60043),
      symbolNamespace: i('symbol-namespace', 60043),
      symbolObject: i('symbol-object', 60043),
      symbolMethod: i('symbol-method', 60044),
      symbolFunction: i('symbol-function', 60044),
      symbolConstructor: i('symbol-constructor', 60044),
      symbolBoolean: i('symbol-boolean', 60047),
      symbolNull: i('symbol-null', 60047),
      symbolNumeric: i('symbol-numeric', 60048),
      symbolNumber: i('symbol-number', 60048),
      symbolStructure: i('symbol-structure', 60049),
      symbolStruct: i('symbol-struct', 60049),
      symbolParameter: i('symbol-parameter', 60050),
      symbolTypeParameter: i('symbol-type-parameter', 60050),
      symbolKey: i('symbol-key', 60051),
      symbolText: i('symbol-text', 60051),
      symbolReference: i('symbol-reference', 60052),
      goToFile: i('go-to-file', 60052),
      symbolEnum: i('symbol-enum', 60053),
      symbolValue: i('symbol-value', 60053),
      symbolRuler: i('symbol-ruler', 60054),
      symbolUnit: i('symbol-unit', 60054),
      activateBreakpoints: i('activate-breakpoints', 60055),
      archive: i('archive', 60056),
      arrowBoth: i('arrow-both', 60057),
      arrowDown: i('arrow-down', 60058),
      arrowLeft: i('arrow-left', 60059),
      arrowRight: i('arrow-right', 60060),
      arrowSmallDown: i('arrow-small-down', 60061),
      arrowSmallLeft: i('arrow-small-left', 60062),
      arrowSmallRight: i('arrow-small-right', 60063),
      arrowSmallUp: i('arrow-small-up', 60064),
      arrowUp: i('arrow-up', 60065),
      bell: i('bell', 60066),
      bold: i('bold', 60067),
      book: i('book', 60068),
      bookmark: i('bookmark', 60069),
      debugBreakpointConditionalUnverified: i('debug-breakpoint-conditional-unverified', 60070),
      debugBreakpointConditional: i('debug-breakpoint-conditional', 60071),
      debugBreakpointConditionalDisabled: i('debug-breakpoint-conditional-disabled', 60071),
      debugBreakpointDataUnverified: i('debug-breakpoint-data-unverified', 60072),
      debugBreakpointData: i('debug-breakpoint-data', 60073),
      debugBreakpointDataDisabled: i('debug-breakpoint-data-disabled', 60073),
      debugBreakpointLogUnverified: i('debug-breakpoint-log-unverified', 60074),
      debugBreakpointLog: i('debug-breakpoint-log', 60075),
      debugBreakpointLogDisabled: i('debug-breakpoint-log-disabled', 60075),
      briefcase: i('briefcase', 60076),
      broadcast: i('broadcast', 60077),
      browser: i('browser', 60078),
      bug: i('bug', 60079),
      calendar: i('calendar', 60080),
      caseSensitive: i('case-sensitive', 60081),
      check: i('check', 60082),
      checklist: i('checklist', 60083),
      chevronDown: i('chevron-down', 60084),
      dropDownButton: i('drop-down-button', 60084),
      chevronLeft: i('chevron-left', 60085),
      chevronRight: i('chevron-right', 60086),
      chevronUp: i('chevron-up', 60087),
      chromeClose: i('chrome-close', 60088),
      chromeMaximize: i('chrome-maximize', 60089),
      chromeMinimize: i('chrome-minimize', 60090),
      chromeRestore: i('chrome-restore', 60091),
      circle: i('circle', 60092),
      circleOutline: i('circle-outline', 60092),
      debugBreakpointUnverified: i('debug-breakpoint-unverified', 60092),
      circleSlash: i('circle-slash', 60093),
      circuitBoard: i('circuit-board', 60094),
      clearAll: i('clear-all', 60095),
      clippy: i('clippy', 60096),
      closeAll: i('close-all', 60097),
      cloudDownload: i('cloud-download', 60098),
      cloudUpload: i('cloud-upload', 60099),
      code: i('code', 60100),
      collapseAll: i('collapse-all', 60101),
      colorMode: i('color-mode', 60102),
      commentDiscussion: i('comment-discussion', 60103),
      compareChanges: i('compare-changes', 60157),
      creditCard: i('credit-card', 60105),
      dash: i('dash', 60108),
      dashboard: i('dashboard', 60109),
      database: i('database', 60110),
      debugContinue: i('debug-continue', 60111),
      debugDisconnect: i('debug-disconnect', 60112),
      debugPause: i('debug-pause', 60113),
      debugRestart: i('debug-restart', 60114),
      debugStart: i('debug-start', 60115),
      debugStepInto: i('debug-step-into', 60116),
      debugStepOut: i('debug-step-out', 60117),
      debugStepOver: i('debug-step-over', 60118),
      debugStop: i('debug-stop', 60119),
      debug: i('debug', 60120),
      deviceCameraVideo: i('device-camera-video', 60121),
      deviceCamera: i('device-camera', 60122),
      deviceMobile: i('device-mobile', 60123),
      diffAdded: i('diff-added', 60124),
      diffIgnored: i('diff-ignored', 60125),
      diffModified: i('diff-modified', 60126),
      diffRemoved: i('diff-removed', 60127),
      diffRenamed: i('diff-renamed', 60128),
      diff: i('diff', 60129),
      discard: i('discard', 60130),
      editorLayout: i('editor-layout', 60131),
      emptyWindow: i('empty-window', 60132),
      exclude: i('exclude', 60133),
      extensions: i('extensions', 60134),
      eyeClosed: i('eye-closed', 60135),
      fileBinary: i('file-binary', 60136),
      fileCode: i('file-code', 60137),
      fileMedia: i('file-media', 60138),
      filePdf: i('file-pdf', 60139),
      fileSubmodule: i('file-submodule', 60140),
      fileSymlinkDirectory: i('file-symlink-directory', 60141),
      fileSymlinkFile: i('file-symlink-file', 60142),
      fileZip: i('file-zip', 60143),
      files: i('files', 60144),
      filter: i('filter', 60145),
      flame: i('flame', 60146),
      foldDown: i('fold-down', 60147),
      foldUp: i('fold-up', 60148),
      fold: i('fold', 60149),
      folderActive: i('folder-active', 60150),
      folderOpened: i('folder-opened', 60151),
      gear: i('gear', 60152),
      gift: i('gift', 60153),
      gistSecret: i('gist-secret', 60154),
      gist: i('gist', 60155),
      gitCommit: i('git-commit', 60156),
      gitCompare: i('git-compare', 60157),
      gitMerge: i('git-merge', 60158),
      githubAction: i('github-action', 60159),
      githubAlt: i('github-alt', 60160),
      globe: i('globe', 60161),
      grabber: i('grabber', 60162),
      graph: i('graph', 60163),
      gripper: i('gripper', 60164),
      heart: i('heart', 60165),
      home: i('home', 60166),
      horizontalRule: i('horizontal-rule', 60167),
      hubot: i('hubot', 60168),
      inbox: i('inbox', 60169),
      issueClosed: i('issue-closed', 60324),
      issueReopened: i('issue-reopened', 60171),
      issues: i('issues', 60172),
      italic: i('italic', 60173),
      jersey: i('jersey', 60174),
      json: i('json', 60175),
      bracket: i('bracket', 60175),
      kebabVertical: i('kebab-vertical', 60176),
      key: i('key', 60177),
      law: i('law', 60178),
      lightbulbAutofix: i('lightbulb-autofix', 60179),
      linkExternal: i('link-external', 60180),
      link: i('link', 60181),
      listOrdered: i('list-ordered', 60182),
      listUnordered: i('list-unordered', 60183),
      liveShare: i('live-share', 60184),
      loading: i('loading', 60185),
      location: i('location', 60186),
      mailRead: i('mail-read', 60187),
      mail: i('mail', 60188),
      markdown: i('markdown', 60189),
      megaphone: i('megaphone', 60190),
      mention: i('mention', 60191),
      milestone: i('milestone', 60192),
      gitPullRequestMilestone: i('git-pull-request-milestone', 60192),
      mortarBoard: i('mortar-board', 60193),
      move: i('move', 60194),
      multipleWindows: i('multiple-windows', 60195),
      mute: i('mute', 60196),
      noNewline: i('no-newline', 60197),
      note: i('note', 60198),
      octoface: i('octoface', 60199),
      openPreview: i('open-preview', 60200),
      package: i('package', 60201),
      paintcan: i('paintcan', 60202),
      pin: i('pin', 60203),
      play: i('play', 60204),
      run: i('run', 60204),
      plug: i('plug', 60205),
      preserveCase: i('preserve-case', 60206),
      preview: i('preview', 60207),
      project: i('project', 60208),
      pulse: i('pulse', 60209),
      question: i('question', 60210),
      quote: i('quote', 60211),
      radioTower: i('radio-tower', 60212),
      reactions: i('reactions', 60213),
      references: i('references', 60214),
      refresh: i('refresh', 60215),
      regex: i('regex', 60216),
      remoteExplorer: i('remote-explorer', 60217),
      remote: i('remote', 60218),
      remove: i('remove', 60219),
      replaceAll: i('replace-all', 60220),
      replace: i('replace', 60221),
      repoClone: i('repo-clone', 60222),
      repoForcePush: i('repo-force-push', 60223),
      repoPull: i('repo-pull', 60224),
      repoPush: i('repo-push', 60225),
      report: i('report', 60226),
      requestChanges: i('request-changes', 60227),
      rocket: i('rocket', 60228),
      rootFolderOpened: i('root-folder-opened', 60229),
      rootFolder: i('root-folder', 60230),
      rss: i('rss', 60231),
      ruby: i('ruby', 60232),
      saveAll: i('save-all', 60233),
      saveAs: i('save-as', 60234),
      save: i('save', 60235),
      screenFull: i('screen-full', 60236),
      screenNormal: i('screen-normal', 60237),
      searchStop: i('search-stop', 60238),
      server: i('server', 60240),
      settingsGear: i('settings-gear', 60241),
      settings: i('settings', 60242),
      shield: i('shield', 60243),
      smiley: i('smiley', 60244),
      sortPrecedence: i('sort-precedence', 60245),
      splitHorizontal: i('split-horizontal', 60246),
      splitVertical: i('split-vertical', 60247),
      squirrel: i('squirrel', 60248),
      starFull: i('star-full', 60249),
      starHalf: i('star-half', 60250),
      symbolClass: i('symbol-class', 60251),
      symbolColor: i('symbol-color', 60252),
      symbolCustomColor: i('symbol-customcolor', 60252),
      symbolConstant: i('symbol-constant', 60253),
      symbolEnumMember: i('symbol-enum-member', 60254),
      symbolField: i('symbol-field', 60255),
      symbolFile: i('symbol-file', 60256),
      symbolInterface: i('symbol-interface', 60257),
      symbolKeyword: i('symbol-keyword', 60258),
      symbolMisc: i('symbol-misc', 60259),
      symbolOperator: i('symbol-operator', 60260),
      symbolProperty: i('symbol-property', 60261),
      wrench: i('wrench', 60261),
      wrenchSubaction: i('wrench-subaction', 60261),
      symbolSnippet: i('symbol-snippet', 60262),
      tasklist: i('tasklist', 60263),
      telescope: i('telescope', 60264),
      textSize: i('text-size', 60265),
      threeBars: i('three-bars', 60266),
      thumbsdown: i('thumbsdown', 60267),
      thumbsup: i('thumbsup', 60268),
      tools: i('tools', 60269),
      triangleDown: i('triangle-down', 60270),
      triangleLeft: i('triangle-left', 60271),
      triangleRight: i('triangle-right', 60272),
      triangleUp: i('triangle-up', 60273),
      twitter: i('twitter', 60274),
      unfold: i('unfold', 60275),
      unlock: i('unlock', 60276),
      unmute: i('unmute', 60277),
      unverified: i('unverified', 60278),
      verified: i('verified', 60279),
      versions: i('versions', 60280),
      vmActive: i('vm-active', 60281),
      vmOutline: i('vm-outline', 60282),
      vmRunning: i('vm-running', 60283),
      watch: i('watch', 60284),
      whitespace: i('whitespace', 60285),
      wholeWord: i('whole-word', 60286),
      window: i('window', 60287),
      wordWrap: i('word-wrap', 60288),
      zoomIn: i('zoom-in', 60289),
      zoomOut: i('zoom-out', 60290),
      listFilter: i('list-filter', 60291),
      listFlat: i('list-flat', 60292),
      listSelection: i('list-selection', 60293),
      selection: i('selection', 60293),
      listTree: i('list-tree', 60294),
      debugBreakpointFunctionUnverified: i('debug-breakpoint-function-unverified', 60295),
      debugBreakpointFunction: i('debug-breakpoint-function', 60296),
      debugBreakpointFunctionDisabled: i('debug-breakpoint-function-disabled', 60296),
      debugStackframeActive: i('debug-stackframe-active', 60297),
      circleSmallFilled: i('circle-small-filled', 60298),
      debugStackframeDot: i('debug-stackframe-dot', 60298),
      debugStackframe: i('debug-stackframe', 60299),
      debugStackframeFocused: i('debug-stackframe-focused', 60299),
      debugBreakpointUnsupported: i('debug-breakpoint-unsupported', 60300),
      symbolString: i('symbol-string', 60301),
      debugReverseContinue: i('debug-reverse-continue', 60302),
      debugStepBack: i('debug-step-back', 60303),
      debugRestartFrame: i('debug-restart-frame', 60304),
      callIncoming: i('call-incoming', 60306),
      callOutgoing: i('call-outgoing', 60307),
      menu: i('menu', 60308),
      expandAll: i('expand-all', 60309),
      feedback: i('feedback', 60310),
      gitPullRequestReviewer: i('git-pull-request-reviewer', 60310),
      groupByRefType: i('group-by-ref-type', 60311),
      ungroupByRefType: i('ungroup-by-ref-type', 60312),
      account: i('account', 60313),
      gitPullRequestAssignee: i('git-pull-request-assignee', 60313),
      bellDot: i('bell-dot', 60314),
      debugConsole: i('debug-console', 60315),
      library: i('library', 60316),
      output: i('output', 60317),
      runAll: i('run-all', 60318),
      syncIgnored: i('sync-ignored', 60319),
      pinned: i('pinned', 60320),
      githubInverted: i('github-inverted', 60321),
      debugAlt: i('debug-alt', 60305),
      serverProcess: i('server-process', 60322),
      serverEnvironment: i('server-environment', 60323),
      pass: i('pass', 60324),
      stopCircle: i('stop-circle', 60325),
      playCircle: i('play-circle', 60326),
      record: i('record', 60327),
      debugAltSmall: i('debug-alt-small', 60328),
      vmConnect: i('vm-connect', 60329),
      cloud: i('cloud', 60330),
      merge: i('merge', 60331),
      exportIcon: i('export', 60332),
      graphLeft: i('graph-left', 60333),
      magnet: i('magnet', 60334),
      notebook: i('notebook', 60335),
      redo: i('redo', 60336),
      checkAll: i('check-all', 60337),
      pinnedDirty: i('pinned-dirty', 60338),
      passFilled: i('pass-filled', 60339),
      circleLargeFilled: i('circle-large-filled', 60340),
      circleLarge: i('circle-large', 60341),
      circleLargeOutline: i('circle-large-outline', 60341),
      combine: i('combine', 60342),
      gather: i('gather', 60342),
      table: i('table', 60343),
      variableGroup: i('variable-group', 60344),
      typeHierarchy: i('type-hierarchy', 60345),
      typeHierarchySub: i('type-hierarchy-sub', 60346),
      typeHierarchySuper: i('type-hierarchy-super', 60347),
      gitPullRequestCreate: i('git-pull-request-create', 60348),
      runAbove: i('run-above', 60349),
      runBelow: i('run-below', 60350),
      notebookTemplate: i('notebook-template', 60351),
      debugRerun: i('debug-rerun', 60352),
      workspaceTrusted: i('workspace-trusted', 60353),
      workspaceUntrusted: i('workspace-untrusted', 60354),
      workspaceUnspecified: i('workspace-unspecified', 60355),
      terminalCmd: i('terminal-cmd', 60356),
      terminalDebian: i('terminal-debian', 60357),
      terminalLinux: i('terminal-linux', 60358),
      terminalPowershell: i('terminal-powershell', 60359),
      terminalTmux: i('terminal-tmux', 60360),
      terminalUbuntu: i('terminal-ubuntu', 60361),
      terminalBash: i('terminal-bash', 60362),
      arrowSwap: i('arrow-swap', 60363),
      copy: i('copy', 60364),
      personAdd: i('person-add', 60365),
      filterFilled: i('filter-filled', 60366),
      wand: i('wand', 60367),
      debugLineByLine: i('debug-line-by-line', 60368),
      inspect: i('inspect', 60369),
      layers: i('layers', 60370),
      layersDot: i('layers-dot', 60371),
      layersActive: i('layers-active', 60372),
      compass: i('compass', 60373),
      compassDot: i('compass-dot', 60374),
      compassActive: i('compass-active', 60375),
      azure: i('azure', 60376),
      issueDraft: i('issue-draft', 60377),
      gitPullRequestClosed: i('git-pull-request-closed', 60378),
      gitPullRequestDraft: i('git-pull-request-draft', 60379),
      debugAll: i('debug-all', 60380),
      debugCoverage: i('debug-coverage', 60381),
      runErrors: i('run-errors', 60382),
      folderLibrary: i('folder-library', 60383),
      debugContinueSmall: i('debug-continue-small', 60384),
      beakerStop: i('beaker-stop', 60385),
      graphLine: i('graph-line', 60386),
      graphScatter: i('graph-scatter', 60387),
      pieChart: i('pie-chart', 60388),
      bracketDot: i('bracket-dot', 60389),
      bracketError: i('bracket-error', 60390),
      lockSmall: i('lock-small', 60391),
      azureDevops: i('azure-devops', 60392),
      verifiedFilled: i('verified-filled', 60393),
      newLine: i('newline', 60394),
      layout: i('layout', 60395),
      layoutActivitybarLeft: i('layout-activitybar-left', 60396),
      layoutActivitybarRight: i('layout-activitybar-right', 60397),
      layoutPanelLeft: i('layout-panel-left', 60398),
      layoutPanelCenter: i('layout-panel-center', 60399),
      layoutPanelJustify: i('layout-panel-justify', 60400),
      layoutPanelRight: i('layout-panel-right', 60401),
      layoutPanel: i('layout-panel', 60402),
      layoutSidebarLeft: i('layout-sidebar-left', 60403),
      layoutSidebarRight: i('layout-sidebar-right', 60404),
      layoutStatusbar: i('layout-statusbar', 60405),
      layoutMenubar: i('layout-menubar', 60406),
      layoutCentered: i('layout-centered', 60407),
      layoutSidebarRightOff: i('layout-sidebar-right-off', 60416),
      layoutPanelOff: i('layout-panel-off', 60417),
      layoutSidebarLeftOff: i('layout-sidebar-left-off', 60418),
      target: i('target', 60408),
      indent: i('indent', 60409),
      recordSmall: i('record-small', 60410),
      errorSmall: i('error-small', 60411),
      arrowCircleDown: i('arrow-circle-down', 60412),
      arrowCircleLeft: i('arrow-circle-left', 60413),
      arrowCircleRight: i('arrow-circle-right', 60414),
      arrowCircleUp: i('arrow-circle-up', 60415),
      heartFilled: i('heart-filled', 60420),
      map: i('map', 60421),
      mapFilled: i('map-filled', 60422),
      circleSmall: i('circle-small', 60423),
      bellSlash: i('bell-slash', 60424),
      bellSlashDot: i('bell-slash-dot', 60425),
      commentUnresolved: i('comment-unresolved', 60426),
      gitPullRequestGoToChanges: i('git-pull-request-go-to-changes', 60427),
      gitPullRequestNewChanges: i('git-pull-request-new-changes', 60428),
      searchFuzzy: i('search-fuzzy', 60429),
      commentDraft: i('comment-draft', 60430),
      send: i('send', 60431),
      sparkle: i('sparkle', 60432),
      insert: i('insert', 60433),
      mic: i('mic', 60434),
      thumbsDownFilled: i('thumbsdown-filled', 60435),
      thumbsUpFilled: i('thumbsup-filled', 60436),
      coffee: i('coffee', 60437),
      snake: i('snake', 60438),
      game: i('game', 60439),
      vr: i('vr', 60440),
      chip: i('chip', 60441),
      piano: i('piano', 60442),
      music: i('music', 60443),
      micFilled: i('mic-filled', 60444),
      gitFetch: i('git-fetch', 60445),
      copilot: i('copilot', 60446),
      lightbulbSparkle: i('lightbulb-sparkle', 60447),
      lightbulbSparkleAutofix: i('lightbulb-sparkle-autofix', 60447),
      robot: i('robot', 60448),
      sparkleFilled: i('sparkle-filled', 60449),
      diffSingle: i('diff-single', 60450),
      diffMultiple: i('diff-multiple', 60451),
      surroundWith: i('surround-with', 60452),
      gitStash: i('git-stash', 60454),
      gitStashApply: i('git-stash-apply', 60455),
      gitStashPop: i('git-stash-pop', 60456),
      runAllCoverage: i('run-all-coverage', 60461),
      runCoverage: i('run-all-coverage', 60460),
      coverage: i('coverage', 60462),
      githubProject: i('github-project', 60463),
      dialogError: i('dialog-error', 'error'),
      dialogWarning: i('dialog-warning', 'warning'),
      dialogInfo: i('dialog-info', 'info'),
      dialogClose: i('dialog-close', 'close'),
      treeItemExpanded: i('tree-item-expanded', 'chevron-down'),
      treeFilterOnTypeOn: i('tree-filter-on-type-on', 'list-filter'),
      treeFilterOnTypeOff: i('tree-filter-on-type-off', 'list-selection'),
      treeFilterClear: i('tree-filter-clear', 'close'),
      treeItemLoading: i('tree-item-loading', 'loading'),
      menuSelection: i('menu-selection', 'check'),
      menuSubmenu: i('menu-submenu', 'chevron-right'),
      menuBarMore: i('menubar-more', 'more'),
      scrollbarButtonLeft: i('scrollbar-button-left', 'triangle-left'),
      scrollbarButtonRight: i('scrollbar-button-right', 'triangle-right'),
      scrollbarButtonUp: i('scrollbar-button-up', 'triangle-up'),
      scrollbarButtonDown: i('scrollbar-button-down', 'triangle-down'),
      toolBarMore: i('toolbar-more', 'more'),
      quickInputBack: i('quick-input-back', 'arrow-left'),
    };
  }), X(J[14], Z([0, 1, 25]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.createProxyObject = t.getAllMethodNames = t.getAllPropertyNames = t.equals = t.mixin = t.cloneAndChange = t.deepFreeze = t.deepClone = void 0;

    function R(l) {
      if (!l || typeof l != 'object' || l instanceof RegExp) return l;
      const u = Array.isArray(l) ? [] : {};
      return Object.entries(l)
        .forEach(([b, f]) => {
          u[b] = f && typeof f == 'object' ? R(f) : f;
        }), u;
    }

    t.deepClone = R;

    function i(l) {
      if (!l || typeof l != 'object') return l;
      const u = [l];
      for (; u.length > 0;) {
        const b = u.shift();
        Object.freeze(b);
        for (const f in b) {
          if (d.call(b, f)) {
            const y = b[f];
            typeof y == 'object' && !Object.isFrozen(y) && !(0, M.isTypedArray)(y) && u.push(y);
          }
        }
      }
      return l;
    }

    t.deepFreeze = i;
    const d = Object.prototype.hasOwnProperty;

    function _(l, u) {
      return p(l, u, new Set);
    }

    t.cloneAndChange = _;

    function p(l, u, b) {
      if ((0, M.isUndefinedOrNull)(l)) return l;
      const f = u(l);
      if (typeof f < 'u') return f;
      if (Array.isArray(l)) {
        const y = [];
        for (const w of l) y.push(p(w, u, b));
        return y;
      }
      if ((0, M.isObject)(l)) {
        if (b.has(l)) throw new Error('Cannot clone recursive data-structure');
        b.add(l);
        const y = {};
        for (const w in l) d.call(l, w) && (y[w] = p(l[w], u, b));
        return b.delete(l), y;
      }
      return l;
    }

    function c(l, u, b = !0) {
      return (0, M.isObject)(l) ? ((0, M.isObject)(u) && Object.keys(u)
        .forEach(f => {
          f in l ? b && ((0, M.isObject)(l[f]) && (0, M.isObject)(u[f]) ? c(l[f], u[f], b) : l[f] = u[f]) : l[f] = u[f];
        }), l) : u;
    }

    t.mixin = c;

    function o(l, u) {
      if (l === u) return !0;
      if (l == null || u === null || u === void 0 || typeof l != typeof u || typeof l != 'object' || Array.isArray(l) !== Array.isArray(u)) return !1;
      let b,
        f;
      if (Array.isArray(l)) {
        if (l.length !== u.length) return !1;
        for (b = 0; b < l.length; b++) if (!o(l[b], u[b])) return !1;
      } else {
        const y = [];
        for (f in l) y.push(f);
        y.sort();
        const w = [];
        for (f in u) w.push(f);
        if (w.sort(), !o(y, w)) return !1;
        for (b = 0; b < y.length; b++) if (!o(l[y[b]], u[y[b]])) return !1;
      }
      return !0;
    }

    t.equals = o;

    function L(l) {
      let u = [];
      for (; Object.prototype !== l;) u = u.concat(Object.getOwnPropertyNames(l)), l = Object.getPrototypeOf(l);
      return u;
    }

    t.getAllPropertyNames = L;

    function e(l) {
      const u = [];
      for (const b of L(l)) typeof l[b] == 'function' && u.push(b);
      return u;
    }

    t.getAllMethodNames = e;

    function s(l, u) {
      const b = y => function() {
          const w = Array.prototype.slice.call(arguments, 0);
          return u(y, w);
        },
        f = {};
      for (const y of l) f[y] = b(y);
      return f;
    }

    t.createProxyObject = s;
  }), X(J[26], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.toUint32 = t.toUint8 = void 0;

    function M(i) {
      return i < 0 ? 0 : i > 255 ? 255 : i | 0;
    }

    t.toUint8 = M;

    function R(i) {
      return i < 0 ? 0 : i > 4294967295 ? 4294967295 : i | 0;
    }

    t.toUint32 = R;
  }), X(J[27], Z([0, 1, 26]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.CharacterSet = t.CharacterClassifier = void 0;

    class R {
      constructor(_) {
        const p = (0, M.toUint8)(_);
        this._defaultValue = p, this._asciiMap = R._createAsciiMap(p), this._map = new Map;
      }

      static _createAsciiMap(_) {
        const p = new Uint8Array(256);
        return p.fill(_), p;
      }

      set(_, p) {
        const c = (0, M.toUint8)(p);
        _ >= 0 && _ < 256 ? this._asciiMap[_] = c : this._map.set(_, c);
      }

      get(_) {
        return _ >= 0 && _ < 256 ? this._asciiMap[_] : this._map.get(_) || this._defaultValue;
      }

      clear() {
        this._asciiMap.fill(this._defaultValue), this._map.clear();
      }
    }

    t.CharacterClassifier = R;

    class i {
      constructor() {
        this._actual = new R(0);
      }

      add(_) {
        this._actual.set(_, 1);
      }

      has(_) {
        return this._actual.get(_) === 1;
      }

      clear() {
        return this._actual.clear();
      }
    }

    t.CharacterSet = i;
  }), X(J[3], Z([0, 1, 5]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.OffsetRangeSet = t.OffsetRange = void 0;

    class R {
      constructor(_, p) {
        if (this.start = _, this.endExclusive = p, _ > p) throw new M.BugIndicatingError(`Invalid range: ${this.toString()}`);
      }

      get isEmpty() {
        return this.start === this.endExclusive;
      }

      get length() {
        return this.endExclusive - this.start;
      }

      static addRange(_, p) {
        let c = 0;
        for (; c < p.length && p[c].endExclusive < _.start;) c++;
        let o = c;
        for (; o < p.length && p[o].start <= _.endExclusive;) o++;
        if (c === o) {
          p.splice(c, 0, _);
        } else {
          const L = Math.min(_.start, p[c].start),
            e = Math.max(_.endExclusive, p[o - 1].endExclusive);
          p.splice(c, o - c, new R(L, e));
        }
      }

      static ofLength(_) {
        return new R(0, _);
      }

      static ofStartAndLength(_, p) {
        return new R(_, _ + p);
      }

      delta(_) {
        return new R(this.start + _, this.endExclusive + _);
      }

      deltaStart(_) {
        return new R(this.start + _, this.endExclusive);
      }

      deltaEnd(_) {
        return new R(this.start, this.endExclusive + _);
      }

      toString() {
        return `[${this.start}, ${this.endExclusive})`;
      }

      contains(_) {
        return this.start <= _ && _ < this.endExclusive;
      }

      join(_) {
        return new R(Math.min(this.start, _.start), Math.max(this.endExclusive, _.endExclusive));
      }

      intersect(_) {
        const p = Math.max(this.start, _.start),
          c = Math.min(this.endExclusive, _.endExclusive);
        if (p <= c) return new R(p, c);
      }

      intersects(_) {
        const p = Math.max(this.start, _.start),
          c = Math.min(this.endExclusive, _.endExclusive);
        return p < c;
      }

      isBefore(_) {
        return this.endExclusive <= _.start;
      }

      isAfter(_) {
        return this.start >= _.endExclusive;
      }

      slice(_) {
        return _.slice(this.start, this.endExclusive);
      }

      clip(_) {
        if (this.isEmpty) throw new M.BugIndicatingError(`Invalid clipping range: ${this.toString()}`);
        return Math.max(this.start, Math.min(this.endExclusive - 1, _));
      }

      clipCyclic(_) {
        if (this.isEmpty) throw new M.BugIndicatingError(`Invalid clipping range: ${this.toString()}`);
        return _ < this.start ? this.endExclusive - (this.start - _) % this.length : _ >= this.endExclusive ? this.start + (_ - this.start) % this.length : _;
      }

      forEach(_) {
        for (let p = this.start; p < this.endExclusive; p++) _(p);
      }
    }

    t.OffsetRange = R;

    class i {
      constructor() {
        this._sortedRanges = [];
      }

      get length() {
        return this._sortedRanges.reduce((_, p) => _ + p.length, 0);
      }

      addRange(_) {
        let p = 0;
        for (; p < this._sortedRanges.length && this._sortedRanges[p].endExclusive < _.start;) p++;
        let c = p;
        for (; c < this._sortedRanges.length && this._sortedRanges[c].start <= _.endExclusive;) c++;
        if (p === c) {
          this._sortedRanges.splice(p, 0, _);
        } else {
          const o = Math.min(_.start, this._sortedRanges[p].start),
            L = Math.max(_.endExclusive, this._sortedRanges[c - 1].endExclusive);
          this._sortedRanges.splice(p, c - p, new R(o, L));
        }
      }

      toString() {
        return this._sortedRanges.map(_ => _.toString())
          .join(', ');
      }

      intersectsStrict(_) {
        let p = 0;
        for (; p < this._sortedRanges.length && this._sortedRanges[p].endExclusive <= _.start;) p++;
        return p < this._sortedRanges.length && this._sortedRanges[p].start < _.endExclusive;
      }

      intersectWithRange(_) {
        const p = new i;
        for (const c of this._sortedRanges) {
          const o = c.intersect(_);
          o && p.addRange(o);
        }
        return p;
      }

      intersectWithRangeLength(_) {
        return this.intersectWithRange(_).length;
      }
    }

    t.OffsetRangeSet = i;
  }), X(J[4], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Position = void 0;

    class M {
      constructor(i, d) {
        this.lineNumber = i, this.column = d;
      }

      static equals(i, d) {
        return !i && !d ? !0 : !!i && !!d && i.lineNumber === d.lineNumber && i.column === d.column;
      }

      static isBefore(i, d) {
        return i.lineNumber < d.lineNumber ? !0 : d.lineNumber < i.lineNumber ? !1 : i.column < d.column;
      }

      static isBeforeOrEqual(i, d) {
        return i.lineNumber < d.lineNumber ? !0 : d.lineNumber < i.lineNumber ? !1 : i.column <= d.column;
      }

      static compare(i, d) {
        const _ = i.lineNumber | 0,
          p = d.lineNumber | 0;
        if (_ === p) {
          const c = i.column | 0,
            o = d.column | 0;
          return c - o;
        }
        return _ - p;
      }

      static lift(i) {
        return new M(i.lineNumber, i.column);
      }

      static isIPosition(i) {
        return i && typeof i.lineNumber == 'number' && typeof i.column == 'number';
      }

      with(i = this.lineNumber, d = this.column) {
        return i === this.lineNumber && d === this.column ? this : new M(i, d);
      }

      delta(i = 0, d = 0) {
        return this.with(this.lineNumber + i, this.column + d);
      }

      equals(i) {
        return M.equals(this, i);
      }

      isBefore(i) {
        return M.isBefore(this, i);
      }

      isBeforeOrEqual(i) {
        return M.isBeforeOrEqual(this, i);
      }

      clone() {
        return new M(this.lineNumber, this.column);
      }

      toString() {
        return '(' + this.lineNumber + ',' + this.column + ')';
      }

      toJSON() {
        return {
          lineNumber: this.lineNumber,
          column: this.column,
        };
      }
    }

    t.Position = M;
  }), X(J[2], Z([0, 1, 4]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Range = void 0;

    class R {
      constructor(d, _, p, c) {
        d > p || d === p && _ > c ? (this.startLineNumber = p, this.startColumn = c, this.endLineNumber = d, this.endColumn = _) : (this.startLineNumber = d, this.startColumn = _, this.endLineNumber = p, this.endColumn = c);
      }

      static isEmpty(d) {
        return d.startLineNumber === d.endLineNumber && d.startColumn === d.endColumn;
      }

      static containsPosition(d, _) {
        return !(_.lineNumber < d.startLineNumber || _.lineNumber > d.endLineNumber || _.lineNumber === d.startLineNumber && _.column < d.startColumn || _.lineNumber === d.endLineNumber && _.column > d.endColumn);
      }

      static strictContainsPosition(d, _) {
        return !(_.lineNumber < d.startLineNumber || _.lineNumber > d.endLineNumber || _.lineNumber === d.startLineNumber && _.column <= d.startColumn || _.lineNumber === d.endLineNumber && _.column >= d.endColumn);
      }

      static containsRange(d, _) {
        return !(_.startLineNumber < d.startLineNumber || _.endLineNumber < d.startLineNumber || _.startLineNumber > d.endLineNumber || _.endLineNumber > d.endLineNumber || _.startLineNumber === d.startLineNumber && _.startColumn < d.startColumn || _.endLineNumber === d.endLineNumber && _.endColumn > d.endColumn);
      }

      static strictContainsRange(d, _) {
        return !(_.startLineNumber < d.startLineNumber || _.endLineNumber < d.startLineNumber || _.startLineNumber > d.endLineNumber || _.endLineNumber > d.endLineNumber || _.startLineNumber === d.startLineNumber && _.startColumn <= d.startColumn || _.endLineNumber === d.endLineNumber && _.endColumn >= d.endColumn);
      }

      static plusRange(d, _) {
        let p,
          c,
          o,
          L;
        return _.startLineNumber < d.startLineNumber ? (p = _.startLineNumber, c = _.startColumn) : _.startLineNumber === d.startLineNumber ? (p = _.startLineNumber, c = Math.min(_.startColumn, d.startColumn)) : (p = d.startLineNumber, c = d.startColumn), _.endLineNumber > d.endLineNumber ? (o = _.endLineNumber, L = _.endColumn) : _.endLineNumber === d.endLineNumber ? (o = _.endLineNumber, L = Math.max(_.endColumn, d.endColumn)) : (o = d.endLineNumber, L = d.endColumn), new R(p, c, o, L);
      }

      static intersectRanges(d, _) {
        let p = d.startLineNumber,
          c = d.startColumn,
          o = d.endLineNumber,
          L = d.endColumn;
        const e = _.startLineNumber,
          s = _.startColumn,
          l = _.endLineNumber,
          u = _.endColumn;
        return p < e ? (p = e, c = s) : p === e && (c = Math.max(c, s)), o > l ? (o = l, L = u) : o === l && (L = Math.min(L, u)), p > o || p === o && c > L ? null : new R(p, c, o, L);
      }

      static equalsRange(d, _) {
        return !d && !_ ? !0 : !!d && !!_ && d.startLineNumber === _.startLineNumber && d.startColumn === _.startColumn && d.endLineNumber === _.endLineNumber && d.endColumn === _.endColumn;
      }

      static getEndPosition(d) {
        return new M.Position(d.endLineNumber, d.endColumn);
      }

      static getStartPosition(d) {
        return new M.Position(d.startLineNumber, d.startColumn);
      }

      static collapseToStart(d) {
        return new R(d.startLineNumber, d.startColumn, d.startLineNumber, d.startColumn);
      }

      static collapseToEnd(d) {
        return new R(d.endLineNumber, d.endColumn, d.endLineNumber, d.endColumn);
      }

      static fromPositions(d, _ = d) {
        return new R(d.lineNumber, d.column, _.lineNumber, _.column);
      }

      static lift(d) {
        return d ? new R(d.startLineNumber, d.startColumn, d.endLineNumber, d.endColumn) : null;
      }

      static isIRange(d) {
        return d && typeof d.startLineNumber == 'number' && typeof d.startColumn == 'number' && typeof d.endLineNumber == 'number' && typeof d.endColumn == 'number';
      }

      static areIntersectingOrTouching(d, _) {
        return !(d.endLineNumber < _.startLineNumber || d.endLineNumber === _.startLineNumber && d.endColumn < _.startColumn || _.endLineNumber < d.startLineNumber || _.endLineNumber === d.startLineNumber && _.endColumn < d.startColumn);
      }

      static areIntersecting(d, _) {
        return !(d.endLineNumber < _.startLineNumber || d.endLineNumber === _.startLineNumber && d.endColumn <= _.startColumn || _.endLineNumber < d.startLineNumber || _.endLineNumber === d.startLineNumber && _.endColumn <= d.startColumn);
      }

      static compareRangesUsingStarts(d, _) {
        if (d && _) {
          const o = d.startLineNumber | 0,
            L = _.startLineNumber | 0;
          if (o === L) {
            const e = d.startColumn | 0,
              s = _.startColumn | 0;
            if (e === s) {
              const l = d.endLineNumber | 0,
                u = _.endLineNumber | 0;
              if (l === u) {
                const b = d.endColumn | 0,
                  f = _.endColumn | 0;
                return b - f;
              }
              return l - u;
            }
            return e - s;
          }
          return o - L;
        }
        return (d ? 1 : 0) - (_ ? 1 : 0);
      }

      static compareRangesUsingEnds(d, _) {
        return d.endLineNumber === _.endLineNumber ? d.endColumn === _.endColumn ? d.startLineNumber === _.startLineNumber ? d.startColumn - _.startColumn : d.startLineNumber - _.startLineNumber : d.endColumn - _.endColumn : d.endLineNumber - _.endLineNumber;
      }

      static spansMultipleLines(d) {
        return d.endLineNumber > d.startLineNumber;
      }

      isEmpty() {
        return R.isEmpty(this);
      }

      containsPosition(d) {
        return R.containsPosition(this, d);
      }

      containsRange(d) {
        return R.containsRange(this, d);
      }

      strictContainsRange(d) {
        return R.strictContainsRange(this, d);
      }

      plusRange(d) {
        return R.plusRange(this, d);
      }

      intersectRanges(d) {
        return R.intersectRanges(this, d);
      }

      equalsRange(d) {
        return R.equalsRange(this, d);
      }

      getEndPosition() {
        return R.getEndPosition(this);
      }

      getStartPosition() {
        return R.getStartPosition(this);
      }

      toString() {
        return '[' + this.startLineNumber + ',' + this.startColumn + ' -> ' + this.endLineNumber + ',' + this.endColumn + ']';
      }

      setEndPosition(d, _) {
        return new R(this.startLineNumber, this.startColumn, d, _);
      }

      setStartPosition(d, _) {
        return new R(d, _, this.endLineNumber, this.endColumn);
      }

      collapseToStart() {
        return R.collapseToStart(this);
      }

      collapseToEnd() {
        return R.collapseToEnd(this);
      }

      delta(d) {
        return new R(this.startLineNumber + d, this.startColumn, this.endLineNumber + d, this.endColumn);
      }

      toJSON() {
        return this;
      }
    }

    t.Range = R;
  }), X(J[10], Z([0, 1, 5, 3, 2, 11]), function(q, t, M, R, i, d) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LineRangeSet = t.LineRange = void 0;

    class _ {
      constructor(o, L) {
        if (o > L) throw new M.BugIndicatingError(`startLineNumber ${o} cannot be after endLineNumberExclusive ${L}`);
        this.startLineNumber = o, this.endLineNumberExclusive = L;
      }

      get isEmpty() {
        return this.startLineNumber === this.endLineNumberExclusive;
      }

      get length() {
        return this.endLineNumberExclusive - this.startLineNumber;
      }

      static fromRangeInclusive(o) {
        return new _(o.startLineNumber, o.endLineNumber + 1);
      }

      static joinMany(o) {
        if (o.length === 0) return [];
        let L = new p(o[0].slice());
        for (let e = 1; e < o.length; e++) L = L.getUnion(new p(o[e].slice()));
        return L.ranges;
      }

      static ofLength(o, L) {
        return new _(o, o + L);
      }

      static deserialize(o) {
        return new _(o[0], o[1]);
      }

      contains(o) {
        return this.startLineNumber <= o && o < this.endLineNumberExclusive;
      }

      delta(o) {
        return new _(this.startLineNumber + o, this.endLineNumberExclusive + o);
      }

      deltaLength(o) {
        return new _(this.startLineNumber, this.endLineNumberExclusive + o);
      }

      join(o) {
        return new _(Math.min(this.startLineNumber, o.startLineNumber), Math.max(this.endLineNumberExclusive, o.endLineNumberExclusive));
      }

      toString() {
        return `[${this.startLineNumber},${this.endLineNumberExclusive})`;
      }

      intersect(o) {
        const L = Math.max(this.startLineNumber, o.startLineNumber),
          e = Math.min(this.endLineNumberExclusive, o.endLineNumberExclusive);
        if (L <= e) return new _(L, e);
      }

      intersectsStrict(o) {
        return this.startLineNumber < o.endLineNumberExclusive && o.startLineNumber < this.endLineNumberExclusive;
      }

      overlapOrTouch(o) {
        return this.startLineNumber <= o.endLineNumberExclusive && o.startLineNumber <= this.endLineNumberExclusive;
      }

      equals(o) {
        return this.startLineNumber === o.startLineNumber && this.endLineNumberExclusive === o.endLineNumberExclusive;
      }

      toInclusiveRange() {
        return this.isEmpty ? null : new i.Range(this.startLineNumber, 1, this.endLineNumberExclusive - 1, Number.MAX_SAFE_INTEGER);
      }

      toExclusiveRange() {
        return new i.Range(this.startLineNumber, 1, this.endLineNumberExclusive, 1);
      }

      mapToLineArray(o) {
        const L = [];
        for (let e = this.startLineNumber; e < this.endLineNumberExclusive; e++) L.push(o(e));
        return L;
      }

      forEach(o) {
        for (let L = this.startLineNumber; L < this.endLineNumberExclusive; L++) o(L);
      }

      serialize() {
        return [this.startLineNumber, this.endLineNumberExclusive];
      }

      includes(o) {
        return this.startLineNumber <= o && o < this.endLineNumberExclusive;
      }

      toOffsetRange() {
        return new R.OffsetRange(this.startLineNumber - 1, this.endLineNumberExclusive - 1);
      }
    }

    t.LineRange = _;

    class p {
      constructor(o = []) {
        this._normalizedRanges = o;
      }

      get ranges() {
        return this._normalizedRanges;
      }

      addRange(o) {
        if (o.length === 0) return;
        const L = (0, d.findFirstIdxMonotonousOrArrLen)(this._normalizedRanges, s => s.endLineNumberExclusive >= o.startLineNumber),
          e = (0, d.findLastIdxMonotonous)(this._normalizedRanges, s => s.startLineNumber <= o.endLineNumberExclusive) + 1;
        if (L === e) {
          this._normalizedRanges.splice(L, 0, o);
        } else if (L === e - 1) {
          const s = this._normalizedRanges[L];
          this._normalizedRanges[L] = s.join(o);
        } else {
          const s = this._normalizedRanges[L].join(this._normalizedRanges[e - 1])
            .join(o);
          this._normalizedRanges.splice(L, e - L, s);
        }
      }

      contains(o) {
        const L = (0, d.findLastMonotonous)(this._normalizedRanges, e => e.startLineNumber <= o);
        return !!L && L.endLineNumberExclusive > o;
      }

      intersects(o) {
        const L = (0, d.findLastMonotonous)(this._normalizedRanges, e => e.startLineNumber < o.endLineNumberExclusive);
        return !!L && L.endLineNumberExclusive > o.startLineNumber;
      }

      getUnion(o) {
        if (this._normalizedRanges.length === 0) return o;
        if (o._normalizedRanges.length === 0) return this;
        const L = [];
        let e = 0,
          s = 0,
          l = null;
        for (; e < this._normalizedRanges.length || s < o._normalizedRanges.length;) {
          let u = null;
          if (e < this._normalizedRanges.length && s < o._normalizedRanges.length) {
            const b = this._normalizedRanges[e],
              f = o._normalizedRanges[s];
            b.startLineNumber < f.startLineNumber ? (u = b, e++) : (u = f, s++);
          } else {
            e < this._normalizedRanges.length ? (u = this._normalizedRanges[e], e++) : (u = o._normalizedRanges[s], s++);
          }
          l === null ? l = u : l.endLineNumberExclusive >= u.startLineNumber ? l = new _(l.startLineNumber, Math.max(l.endLineNumberExclusive, u.endLineNumberExclusive)) : (L.push(l), l = u);
        }
        return l !== null && L.push(l), new p(L);
      }

      subtractFrom(o) {
        const L = (0, d.findFirstIdxMonotonousOrArrLen)(this._normalizedRanges, u => u.endLineNumberExclusive >= o.startLineNumber),
          e = (0, d.findLastIdxMonotonous)(this._normalizedRanges, u => u.startLineNumber <= o.endLineNumberExclusive) + 1;
        if (L === e) return new p([o]);
        const s = [];
        let l = o.startLineNumber;
        for (let u = L; u < e; u++) {
          const b = this._normalizedRanges[u];
          b.startLineNumber > l && s.push(new _(l, b.startLineNumber)), l = b.endLineNumberExclusive;
        }
        return l < o.endLineNumberExclusive && s.push(new _(l, o.endLineNumberExclusive)), new p(s);
      }

      toString() {
        return this._normalizedRanges.map(o => o.toString())
          .join(', ');
      }

      getIntersection(o) {
        const L = [];
        let e = 0,
          s = 0;
        for (; e < this._normalizedRanges.length && s < o._normalizedRanges.length;) {
          const l = this._normalizedRanges[e],
            u = o._normalizedRanges[s],
            b = l.intersect(u);
          b && !b.isEmpty && L.push(b), l.endLineNumberExclusive < u.endLineNumberExclusive ? e++ : s++;
        }
        return new p(L);
      }

      getWithDelta(o) {
        return new p(this._normalizedRanges.map(L => L.delta(o)));
      }
    }

    t.LineRangeSet = p;
  }), X(J[41], Z([0, 1, 4, 2]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Selection = void 0;

    class i extends R.Range {
      constructor(_, p, c, o) {
        super(_, p, c, o), this.selectionStartLineNumber = _, this.selectionStartColumn = p, this.positionLineNumber = c, this.positionColumn = o;
      }

      static selectionsEqual(_, p) {
        return _.selectionStartLineNumber === p.selectionStartLineNumber && _.selectionStartColumn === p.selectionStartColumn && _.positionLineNumber === p.positionLineNumber && _.positionColumn === p.positionColumn;
      }

      static fromPositions(_, p = _) {
        return new i(_.lineNumber, _.column, p.lineNumber, p.column);
      }

      static fromRange(_, p) {
        return p === 0 ? new i(_.startLineNumber, _.startColumn, _.endLineNumber, _.endColumn) : new i(_.endLineNumber, _.endColumn, _.startLineNumber, _.startColumn);
      }

      static liftSelection(_) {
        return new i(_.selectionStartLineNumber, _.selectionStartColumn, _.positionLineNumber, _.positionColumn);
      }

      static selectionsArrEqual(_, p) {
        if (_ && !p || !_ && p) return !1;
        if (!_ && !p) return !0;
        if (_.length !== p.length) return !1;
        for (let c = 0, o = _.length; c < o; c++) if (!this.selectionsEqual(_[c], p[c])) return !1;
        return !0;
      }

      static isISelection(_) {
        return _ && typeof _.selectionStartLineNumber == 'number' && typeof _.selectionStartColumn == 'number' && typeof _.positionLineNumber == 'number' && typeof _.positionColumn == 'number';
      }

      static createWithDirection(_, p, c, o, L) {
        return L === 0 ? new i(_, p, c, o) : new i(c, o, _, p);
      }

      toString() {
        return '[' + this.selectionStartLineNumber + ',' + this.selectionStartColumn + ' -> ' + this.positionLineNumber + ',' + this.positionColumn + ']';
      }

      equalsSelection(_) {
        return i.selectionsEqual(this, _);
      }

      getDirection() {
        return this.selectionStartLineNumber === this.startLineNumber && this.selectionStartColumn === this.startColumn ? 0 : 1;
      }

      setEndPosition(_, p) {
        return this.getDirection() === 0 ? new i(this.startLineNumber, this.startColumn, _, p) : new i(_, p, this.startLineNumber, this.startColumn);
      }

      getPosition() {
        return new M.Position(this.positionLineNumber, this.positionColumn);
      }

      getSelectionStart() {
        return new M.Position(this.selectionStartLineNumber, this.selectionStartColumn);
      }

      setStartPosition(_, p) {
        return this.getDirection() === 0 ? new i(_, p, this.endLineNumber, this.endColumn) : new i(this.endLineNumber, this.endColumn, _, p);
      }
    }

    t.Selection = i;
  }), X(J[42], Z([0, 1, 27]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.getMapForWordSeparators = t.WordCharacterClassifier = void 0;

    class R extends M.CharacterClassifier {
      constructor(_) {
        super(0);
        for (let p = 0, c = _.length; p < c; p++) this.set(_.charCodeAt(p), 2);
        this.set(32, 1), this.set(9, 1);
      }
    }

    t.WordCharacterClassifier = R;

    function i(d) {
      const _ = {};
      return p => (_.hasOwnProperty(p) || (_[p] = d(p)), _[p]);
    }

    t.getMapForWordSeparators = i(d => new R(d));
  }), X(J[28], Z([0, 1, 21, 22]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.getWordAtText = t.ensureValidWordDefinition = t.DEFAULT_WORD_REGEXP = t.USUAL_WORD_SEPARATORS = void 0, t.USUAL_WORD_SEPARATORS = '`~!@#$%^&*()-=+[{]}\\|;:\'",.<>/?';

    function i(o = '') {
      let L = '(-?\\d*\\.\\d\\w*)|([^';
      for (const e of t.USUAL_WORD_SEPARATORS) o.indexOf(e) >= 0 || (L += '\\' + e);
      return L += '\\s]+)', new RegExp(L, 'g');
    }

    t.DEFAULT_WORD_REGEXP = i();

    function d(o) {
      let L = t.DEFAULT_WORD_REGEXP;
      if (o && o instanceof RegExp) {
        if (o.global) {
          L = o;
        } else {
          let e = 'g';
          o.ignoreCase && (e += 'i'), o.multiline && (e += 'm'), o.unicode && (e += 'u'), L = new RegExp(o.source, e);
        }
      }
      return L.lastIndex = 0, L;
    }

    t.ensureValidWordDefinition = d;
    const _ = new R.LinkedList;
    _.unshift({
      maxLen: 1e3,
      windowSize: 15,
      timeBudget: 150,
    });

    function p(o, L, e, s, l) {
      if (L = d(L), l || (l = M.Iterable.first(_)), e.length > l.maxLen) {
        let w = o - l.maxLen / 2;
        return w < 0 ? w = 0 : s += w, e = e.substring(w, o + l.maxLen / 2), p(o, L, e, s, l);
      }
      const u = Date.now(),
        b = o - 1 - s;
      let f = -1,
        y = null;
      for (let w = 1; !(Date.now() - u >= l.timeBudget); w++) {
        const E = b - l.windowSize * w;
        L.lastIndex = Math.max(0, E);
        const S = c(L, e, b, f);
        if (!S && y || (y = S, E <= 0)) break;
        f = E;
      }
      if (y) {
        const w = {
          word: y[0],
          startColumn: s + 1 + y.index,
          endColumn: s + 1 + y.index + y[0].length,
        };
        return L.lastIndex = 0, w;
      }
      return null;
    }

    t.getWordAtText = p;

    function c(o, L, e, s) {
      let l;
      for (; l = o.exec(L);) {
        const u = l.index || 0;
        if (u <= e && o.lastIndex >= e) return l;
        if (s > 0 && u > s) return null;
      }
      return null;
    }
  }), X(J[8], Z([0, 1, 7, 5, 3]), function(q, t, M, R, i) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.DateTimeout = t.InfiniteTimeout = t.OffsetPair = t.SequenceDiff = t.DiffAlgorithmResult = void 0;

    class d {
      constructor(e, s) {
        this.diffs = e, this.hitTimeout = s;
      }

      static trivial(e, s) {
        return new d([new _(i.OffsetRange.ofLength(e.length), i.OffsetRange.ofLength(s.length))], !1);
      }

      static trivialTimedOut(e, s) {
        return new d([new _(i.OffsetRange.ofLength(e.length), i.OffsetRange.ofLength(s.length))], !0);
      }
    }

    t.DiffAlgorithmResult = d;

    class _ {
      constructor(e, s) {
        this.seq1Range = e, this.seq2Range = s;
      }

      static invert(e, s) {
        const l = [];
        return (0, M.forEachAdjacent)(e, (u, b) => {
          l.push(_.fromOffsetPairs(u ? u.getEndExclusives() : p.zero, b ? b.getStarts() : new p(s, (u ? u.seq2Range.endExclusive - u.seq1Range.endExclusive : 0) + s)));
        }), l;
      }

      static fromOffsetPairs(e, s) {
        return new _(new i.OffsetRange(e.offset1, s.offset1), new i.OffsetRange(e.offset2, s.offset2));
      }

      swap() {
        return new _(this.seq2Range, this.seq1Range);
      }

      toString() {
        return `${this.seq1Range} <-> ${this.seq2Range}`;
      }

      join(e) {
        return new _(this.seq1Range.join(e.seq1Range), this.seq2Range.join(e.seq2Range));
      }

      delta(e) {
        return e === 0 ? this : new _(this.seq1Range.delta(e), this.seq2Range.delta(e));
      }

      deltaStart(e) {
        return e === 0 ? this : new _(this.seq1Range.deltaStart(e), this.seq2Range.deltaStart(e));
      }

      deltaEnd(e) {
        return e === 0 ? this : new _(this.seq1Range.deltaEnd(e), this.seq2Range.deltaEnd(e));
      }

      intersect(e) {
        const s = this.seq1Range.intersect(e.seq1Range),
          l = this.seq2Range.intersect(e.seq2Range);
        if (!(!s || !l)) return new _(s, l);
      }

      getStarts() {
        return new p(this.seq1Range.start, this.seq2Range.start);
      }

      getEndExclusives() {
        return new p(this.seq1Range.endExclusive, this.seq2Range.endExclusive);
      }
    }

    t.SequenceDiff = _;

    class p {
      constructor(e, s) {
        this.offset1 = e, this.offset2 = s;
      }

      toString() {
        return `${this.offset1} <-> ${this.offset2}`;
      }

      delta(e) {
        return e === 0 ? this : new p(this.offset1 + e, this.offset2 + e);
      }

      equals(e) {
        return this.offset1 === e.offset1 && this.offset2 === e.offset2;
      }
    }

    t.OffsetPair = p, p.zero = new p(0, 0), p.max = new p(Number.MAX_SAFE_INTEGER, Number.MAX_SAFE_INTEGER);

    class c {
      isValid() {
        return !0;
      }
    }

    t.InfiniteTimeout = c, c.instance = new c;

    class o {
      constructor(e) {
        if (this.timeout = e, this.startTime = Date.now(), this.valid = !0, e <= 0) throw new R.BugIndicatingError('timeout must be positive');
      }

      isValid() {
        if (!(Date.now() - this.startTime < this.timeout) && this.valid) {
          this.valid = !1;
          debugger
        }
        return this.valid;
      }
    }

    t.DateTimeout = o;
  }), X(J[29], Z([0, 1, 3, 8]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.MyersDiffAlgorithm = void 0;

    class i {
      compute(o, L, e = R.InfiniteTimeout.instance) {
        if (o.length === 0 || L.length === 0) return R.DiffAlgorithmResult.trivial(o, L);
        const s = o,
          l = L;

        function u(a, g) {
          for (; a < s.length && g < l.length && s.getElement(a) === l.getElement(g);) a++, g++;
          return a;
        }

        let b = 0;
        const f = new _;
        f.set(0, u(0, 0));
        const y = new p;
        y.set(0, f.get(0) === 0 ? null : new d(null, 0, 0, f.get(0)));
        let w = 0;
        e:for (; ;) {
          if (b++, !e.isValid()) return R.DiffAlgorithmResult.trivialTimedOut(s, l);
          const a = -Math.min(b, l.length + b % 2),
            g = Math.min(b, s.length + b % 2);
          for (w = a; w <= g; w += 2) {
            let m = 0;
            const h = w === g ? -1 : f.get(w + 1),
              v = w === a ? -1 : f.get(w - 1) + 1;
            m++;
            const N = Math.min(Math.max(h, v), s.length),
              A = N - w;
            if (m++, N > s.length || A > l.length) continue;
            const D = u(N, A);
            f.set(w, D);
            const P = N === h ? y.get(w + 1) : y.get(w - 1);
            if (y.set(w, D !== N ? new d(P, N, A, D - N) : P), f.get(w) === s.length && f.get(w) - w === l.length) break e;
          }
        }
        let E = y.get(w);
        const S = [];
        let C = s.length,
          r = l.length;
        for (; ;) {
          const a = E ? E.x + E.length : 0,
            g = E ? E.y + E.length : 0;
          if ((a !== C || g !== r) && S.push(new R.SequenceDiff(new M.OffsetRange(a, C), new M.OffsetRange(g, r))), !E) break;
          C = E.x, r = E.y, E = E.prev;
        }
        return S.reverse(), new R.DiffAlgorithmResult(S, !1);
      }
    }

    t.MyersDiffAlgorithm = i;

    class d {
      constructor(o, L, e, s) {
        this.prev = o, this.x = L, this.y = e, this.length = s;
      }
    }

    class _ {
      constructor() {
        this.positiveArr = new Int32Array(10), this.negativeArr = new Int32Array(10);
      }

      get(o) {
        return o < 0 ? (o = -o - 1, this.negativeArr[o]) : this.positiveArr[o];
      }

      set(o, L) {
        if (o < 0) {
          if (o = -o - 1, o >= this.negativeArr.length) {
            const e = this.negativeArr;
            this.negativeArr = new Int32Array(e.length * 2), this.negativeArr.set(e);
          }
          this.negativeArr[o] = L;
        } else {
          if (o >= this.positiveArr.length) {
            const e = this.positiveArr;
            this.positiveArr = new Int32Array(e.length * 2), this.positiveArr.set(e);
          }
          this.positiveArr[o] = L;
        }
      }
    }

    class p {
      constructor() {
        this.positiveArr = [], this.negativeArr = [];
      }

      get(o) {
        return o < 0 ? (o = -o - 1, this.negativeArr[o]) : this.positiveArr[o];
      }

      set(o, L) {
        o < 0 ? (o = -o - 1, this.negativeArr[o] = L) : this.positiveArr[o] = L;
      }
    }
  }), X(J[43], Z([0, 1, 7, 3, 8]), function(q, t, M, R, i) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.removeVeryShortMatchingTextBetweenLongDiffs = t.removeVeryShortMatchingLinesBetweenDiffs = t.extendDiffsToEntireWordIfAppropriate = t.removeShortMatches = t.optimizeSequenceDiffs = void 0;

    function d(u, b, f) {
      let y = f;
      return y = _(u, b, y), y = _(u, b, y), y = p(u, b, y), y;
    }

    t.optimizeSequenceDiffs = d;

    function _(u, b, f) {
      if (f.length === 0) return f;
      const y = [];
      y.push(f[0]);
      for (let E = 1; E < f.length; E++) {
        const S = y[y.length - 1];
        let C = f[E];
        if (C.seq1Range.isEmpty || C.seq2Range.isEmpty) {
          const r = C.seq1Range.start - S.seq1Range.endExclusive;
          let a;
          for (a = 1; a <= r && !(u.getElement(C.seq1Range.start - a) !== u.getElement(C.seq1Range.endExclusive - a) || b.getElement(C.seq2Range.start - a) !== b.getElement(C.seq2Range.endExclusive - a)); a++) ;
          if (a--, a === r) {
            y[y.length - 1] = new i.SequenceDiff(new R.OffsetRange(S.seq1Range.start, C.seq1Range.endExclusive - r), new R.OffsetRange(S.seq2Range.start, C.seq2Range.endExclusive - r));
            continue;
          }
          C = C.delta(-a);
        }
        y.push(C);
      }
      const w = [];
      for (let E = 0; E < y.length - 1; E++) {
        const S = y[E + 1];
        let C = y[E];
        if (C.seq1Range.isEmpty || C.seq2Range.isEmpty) {
          const r = S.seq1Range.start - C.seq1Range.endExclusive;
          let a;
          for (a = 0; a < r && !(!u.isStronglyEqual(C.seq1Range.start + a, C.seq1Range.endExclusive + a) || !b.isStronglyEqual(C.seq2Range.start + a, C.seq2Range.endExclusive + a)); a++) ;
          if (a === r) {
            y[E + 1] = new i.SequenceDiff(new R.OffsetRange(C.seq1Range.start + r, S.seq1Range.endExclusive), new R.OffsetRange(C.seq2Range.start + r, S.seq2Range.endExclusive));
            continue;
          }
          a > 0 && (C = C.delta(a));
        }
        w.push(C);
      }
      return y.length > 0 && w.push(y[y.length - 1]), w;
    }

    function p(u, b, f) {
      if (!u.getBoundaryScore || !b.getBoundaryScore) return f;
      for (let y = 0; y < f.length; y++) {
        const w = y > 0 ? f[y - 1] : void 0,
          E = f[y],
          S = y + 1 < f.length ? f[y + 1] : void 0,
          C = new R.OffsetRange(w ? w.seq1Range.endExclusive + 1 : 0, S ? S.seq1Range.start - 1 : u.length),
          r = new R.OffsetRange(w ? w.seq2Range.endExclusive + 1 : 0, S ? S.seq2Range.start - 1 : b.length);
        E.seq1Range.isEmpty ? f[y] = c(E, u, b, C, r) : E.seq2Range.isEmpty && (f[y] = c(E.swap(), b, u, r, C)
          .swap());
      }
      return f;
    }

    function c(u, b, f, y, w) {
      let S = 1;
      for (; u.seq1Range.start - S >= y.start && u.seq2Range.start - S >= w.start && f.isStronglyEqual(u.seq2Range.start - S, u.seq2Range.endExclusive - S) && S < 100;) S++;
      S--;
      let C = 0;
      for (; u.seq1Range.start + C < y.endExclusive && u.seq2Range.endExclusive + C < w.endExclusive && f.isStronglyEqual(u.seq2Range.start + C, u.seq2Range.endExclusive + C) && C < 100;) C++;
      if (S === 0 && C === 0) return u;
      let r = 0,
        a = -1;
      for (let g = -S; g <= C; g++) {
        const m = u.seq2Range.start + g,
          h = u.seq2Range.endExclusive + g,
          v = u.seq1Range.start + g,
          N = b.getBoundaryScore(v) + f.getBoundaryScore(m) + f.getBoundaryScore(h);
        N > a && (a = N, r = g);
      }
      return u.delta(r);
    }

    function o(u, b, f) {
      const y = [];
      for (const w of f) {
        const E = y[y.length - 1];
        if (!E) {
          y.push(w);
          continue;
        }
        w.seq1Range.start - E.seq1Range.endExclusive <= 2 || w.seq2Range.start - E.seq2Range.endExclusive <= 2 ? y[y.length - 1] = new i.SequenceDiff(E.seq1Range.join(w.seq1Range), E.seq2Range.join(w.seq2Range)) : y.push(w);
      }
      return y;
    }

    t.removeShortMatches = o;

    function L(u, b, f) {
      const y = i.SequenceDiff.invert(f, u.length),
        w = [];
      let E = new i.OffsetPair(0, 0);

      function S(r, a) {
        if (r.offset1 < E.offset1 || r.offset2 < E.offset2) return;
        const g = u.findWordContaining(r.offset1),
          m = b.findWordContaining(r.offset2);
        if (!g || !m) return;
        let h = new i.SequenceDiff(g, m);
        const v = h.intersect(a);
        let N = v.seq1Range.length,
          A = v.seq2Range.length;
        for (; y.length > 0;) {
          const D = y[0];
          if (!(D.seq1Range.intersects(g) || D.seq2Range.intersects(m))) break;
          const T = u.findWordContaining(D.seq1Range.start),
            I = b.findWordContaining(D.seq2Range.start),
            B = new i.SequenceDiff(T, I),
            z = B.intersect(D);
          if (N += z.seq1Range.length, A += z.seq2Range.length, h = h.join(B), h.seq1Range.endExclusive >= D.seq1Range.endExclusive) y.shift(); else break;
        }
        N + A < (h.seq1Range.length + h.seq2Range.length) * 2 / 3 && w.push(h), E = h.getEndExclusives();
      }

      for (; y.length > 0;) {
        const r = y.shift();
        r.seq1Range.isEmpty || (S(r.getStarts(), r), S(r.getEndExclusives()
          .delta(-1), r));
      }
      return e(f, w);
    }

    t.extendDiffsToEntireWordIfAppropriate = L;

    function e(u, b) {
      const f = [];
      for (; u.length > 0 || b.length > 0;) {
        const y = u[0],
          w = b[0];
        let E;
        y && (!w || y.seq1Range.start < w.seq1Range.start) ? E = u.shift() : E = b.shift(), f.length > 0 && f[f.length - 1].seq1Range.endExclusive >= E.seq1Range.start ? f[f.length - 1] = f[f.length - 1].join(E) : f.push(E);
      }
      return f;
    }

    function s(u, b, f) {
      let y = f;
      if (y.length === 0) return y;
      let w = 0,
        E;
      do {
        E = !1;
        const S = [y[0]];
        for (let C = 1; C < y.length; C++) {
          let g = function(h, v) {
            const N = new R.OffsetRange(a.seq1Range.endExclusive, r.seq1Range.start);
            return u.getText(N)
              .replace(/\s/g, '').length <= 4 && (h.seq1Range.length + h.seq2Range.length > 5 || v.seq1Range.length + v.seq2Range.length > 5);
          };
          const r = y[C],
            a = S[S.length - 1];
          g(a, r) ? (E = !0, S[S.length - 1] = S[S.length - 1].join(r)) : S.push(r);
        }
        y = S;
      } while (w++ < 10 && E);
      return y;
    }

    t.removeVeryShortMatchingLinesBetweenDiffs = s;

    function l(u, b, f) {
      let y = f;
      if (y.length === 0) return y;
      let w = 0,
        E;
      do {
        E = !1;
        const C = [y[0]];
        for (let r = 1; r < y.length; r++) {
          let m = function(v, N) {
            const A = new R.OffsetRange(g.seq1Range.endExclusive, a.seq1Range.start);
            if (u.countLinesIn(A) > 5 || A.length > 500) return !1;
            const P = u.getText(A)
              .trim();
            if (P.length > 20 || P.split(/\r\n|\r|\n/).length > 1) return !1;
            const T = u.countLinesIn(v.seq1Range),
              I = v.seq1Range.length,
              B = b.countLinesIn(v.seq2Range),
              z = v.seq2Range.length,
              x = u.countLinesIn(N.seq1Range),
              O = N.seq1Range.length,
              F = b.countLinesIn(N.seq2Range),
              W = N.seq2Range.length,
              H = 2 * 40 + 50;

            function G(ne) {
              return Math.min(ne, H);
            }

            return Math.pow(Math.pow(G(T * 40 + I), 1.5) + Math.pow(G(B * 40 + z), 1.5), 1.5) + Math.pow(Math.pow(G(x * 40 + O), 1.5) + Math.pow(G(F * 40 + W), 1.5), 1.5) > (H ** 1.5) ** 1.5 * 1.3;
          };
          const a = y[r],
            g = C[C.length - 1];
          m(g, a) ? (E = !0, C[C.length - 1] = C[C.length - 1].join(a)) : C.push(a);
        }
        y = C;
      } while (w++ < 10 && E);
      const S = [];
      return (0, M.forEachWithNeighbors)(y, (C, r, a) => {
        let g = r;

        function m(P) {
          return P.length > 0 && P.trim().length <= 3 && r.seq1Range.length + r.seq2Range.length > 100;
        }

        const h = u.extendToFullLines(r.seq1Range),
          v = u.getText(new R.OffsetRange(h.start, r.seq1Range.start));
        m(v) && (g = g.deltaStart(-v.length));
        const N = u.getText(new R.OffsetRange(r.seq1Range.endExclusive, h.endExclusive));
        m(N) && (g = g.deltaEnd(N.length));
        const A = i.SequenceDiff.fromOffsetPairs(C ? C.getEndExclusives() : i.OffsetPair.zero, a ? a.getStarts() : i.OffsetPair.max),
          D = g.intersect(A);
        S.length > 0 && D.getStarts()
          .equals(S[S.length - 1].getEndExclusives()) ? S[S.length - 1] = S[S.length - 1].join(D) : S.push(D);
      }), S;
    }

    t.removeVeryShortMatchingTextBetweenLongDiffs = l;
  }), X(J[44], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LineSequence = void 0;

    class M {
      constructor(d, _) {
        this.trimmedHash = d, this.lines = _;
      }

      get length() {
        return this.trimmedHash.length;
      }

      getElement(d) {
        return this.trimmedHash[d];
      }

      getBoundaryScore(d) {
        const _ = d === 0 ? 0 : R(this.lines[d - 1]),
          p = d === this.lines.length ? 0 : R(this.lines[d]);
        return 1e3 - (_ + p);
      }

      getText(d) {
        return this.lines.slice(d.start, d.endExclusive)
          .join(`
`);
      }

      isStronglyEqual(d, _) {
        return this.lines[d] === this.lines[_];
      }
    }

    t.LineSequence = M;

    function R(i) {
      let d = 0;
      for (; d < i.length && (i.charCodeAt(d) === 32 || i.charCodeAt(d) === 9);) d++;
      return d;
    }
  }), X(J[15], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LineRangeFragment = t.isSpace = t.Array2D = void 0;

    class M {
      constructor(_, p) {
        this.width = _, this.height = p, this.array = [], this.array = new Array(_ * p);
      }

      get(_, p) {
        return this.array[_ + p * this.width];
      }

      set(_, p, c) {
        this.array[_ + p * this.width] = c;
      }
    }

    t.Array2D = M;

    function R(d) {
      return d === 32 || d === 9;
    }

    t.isSpace = R;

    class i {
      constructor(_, p, c) {
        this.range = _, this.lines = p, this.source = c, this.histogram = [];
        let o = 0;
        for (let L = _.startLineNumber - 1; L < _.endLineNumberExclusive - 1; L++) {
          const e = p[L];
          for (let l = 0; l < e.length; l++) {
            o++;
            const u = e[l],
              b = i.getKey(u);
            this.histogram[b] = (this.histogram[b] || 0) + 1;
          }
          o++;
          const s = i.getKey(`
`);
          this.histogram[s] = (this.histogram[s] || 0) + 1;
        }
        this.totalCount = o;
      }

      static getKey(_) {
        let p = this.chrKeys.get(_);
        return p === void 0 && (p = this.chrKeys.size, this.chrKeys.set(_, p)), p;
      }

      computeSimilarity(_) {
        var p,
          c;
        let o = 0;
        const L = Math.max(this.histogram.length, _.histogram.length);
        for (let e = 0; e < L; e++) o += Math.abs(((p = this.histogram[e]) !== null && p !== void 0 ? p : 0) - ((c = _.histogram[e]) !== null && c !== void 0 ? c : 0));
        return 1 - o / (this.totalCount + _.totalCount);
      }
    }

    t.LineRangeFragment = i, i.chrKeys = new Map;
  }), X(J[45], Z([0, 1, 3, 8, 15]), function(q, t, M, R, i) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.DynamicProgrammingDiffing = void 0;

    class d {
      compute(p, c, o = R.InfiniteTimeout.instance, L) {
        if (p.length === 0 || c.length === 0) return R.DiffAlgorithmResult.trivial(p, c);
        const e = new i.Array2D(p.length, c.length),
          s = new i.Array2D(p.length, c.length),
          l = new i.Array2D(p.length, c.length);
        for (let S = 0; S < p.length; S++) {
          for (let C = 0; C < c.length; C++) {
            if (!o.isValid()) return R.DiffAlgorithmResult.trivialTimedOut(p, c);
            const r = S === 0 ? 0 : e.get(S - 1, C),
              a = C === 0 ? 0 : e.get(S, C - 1);
            let g;
            p.getElement(S) === c.getElement(C) ? (S === 0 || C === 0 ? g = 0 : g = e.get(S - 1, C - 1), S > 0 && C > 0 && s.get(S - 1, C - 1) === 3 && (g += l.get(S - 1, C - 1)), g += L ? L(S, C) : 1) : g = -1;
            const m = Math.max(r, a, g);
            if (m === g) {
              const h = S > 0 && C > 0 ? l.get(S - 1, C - 1) : 0;
              l.set(S, C, h + 1), s.set(S, C, 3);
            } else {
              m === r ? (l.set(S, C, 0), s.set(S, C, 1)) : m === a && (l.set(S, C, 0), s.set(S, C, 2));
            }
            e.set(S, C, m);
          }
        }
        const u = [];
        let b = p.length,
          f = c.length;

        function y(S, C) {
          (S + 1 !== b || C + 1 !== f) && u.push(new R.SequenceDiff(new M.OffsetRange(S + 1, b), new M.OffsetRange(C + 1, f))), b = S, f = C;
        }

        let w = p.length - 1,
          E = c.length - 1;
        for (; w >= 0 && E >= 0;) s.get(w, E) === 3 ? (y(w, E), w--, E--) : s.get(w, E) === 1 ? w-- : E--;
        return y(-1, -1), u.reverse(), new R.DiffAlgorithmResult(u, !1);
      }
    }

    t.DynamicProgrammingDiffing = d;
  }), X(J[30], Z([0, 1, 11, 3, 4, 2, 15]), function(q, t, M, R, i, d, _) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.LinesSliceCharSequence = void 0;

    class p {
      constructor(l, u, b) {
        this.lines = l, this.considerWhitespaceChanges = b, this.elements = [], this.firstCharOffsetByLine = [], this.additionalOffsetByLine = [];
        let f = !1;
        u.start > 0 && u.endExclusive >= l.length && (u = new R.OffsetRange(u.start - 1, u.endExclusive), f = !0), this.lineRange = u, this.firstCharOffsetByLine[0] = 0;
        for (let y = this.lineRange.start; y < this.lineRange.endExclusive; y++) {
          let w = l[y],
            E = 0;
          if (f) {
            E = w.length, w = '', f = !1;
          } else if (!b) {
            const S = w.trimStart();
            E = w.length - S.length, w = S.trimEnd();
          }
          this.additionalOffsetByLine.push(E);
          for (let S = 0; S < w.length; S++) this.elements.push(w.charCodeAt(S));
          y < l.length - 1 && (this.elements.push(10), this.firstCharOffsetByLine[y - this.lineRange.start + 1] = this.elements.length);
        }
        this.additionalOffsetByLine.push(0);
      }

      get text() {
        return this.getText(new R.OffsetRange(0, this.length));
      }

      get length() {
        return this.elements.length;
      }

      toString() {
        return `Slice: "${this.text}"`;
      }

      getText(l) {
        return this.elements.slice(l.start, l.endExclusive)
          .map(u => String.fromCharCode(u))
          .join('');
      }

      getElement(l) {
        return this.elements[l];
      }

      getBoundaryScore(l) {
        const u = e(l > 0 ? this.elements[l - 1] : -1),
          b = e(l < this.elements.length ? this.elements[l] : -1);
        if (u === 7 && b === 8) return 0;
        if (u === 8) return 150;
        let f = 0;
        return u !== b && (f += 10, u === 0 && b === 1 && (f += 1)), f += L(u), f += L(b), f;
      }

      translateOffset(l) {
        if (this.lineRange.isEmpty) return new i.Position(this.lineRange.start + 1, 1);
        const u = (0, M.findLastIdxMonotonous)(this.firstCharOffsetByLine, b => b <= l);
        return new i.Position(this.lineRange.start + u + 1, l - this.firstCharOffsetByLine[u] + this.additionalOffsetByLine[u] + 1);
      }

      translateRange(l) {
        return d.Range.fromPositions(this.translateOffset(l.start), this.translateOffset(l.endExclusive));
      }

      findWordContaining(l) {
        if (l < 0 || l >= this.elements.length || !c(this.elements[l])) return;
        let u = l;
        for (; u > 0 && c(this.elements[u - 1]);) u--;
        let b = l;
        for (; b < this.elements.length && c(this.elements[b]);) b++;
        return new R.OffsetRange(u, b);
      }

      countLinesIn(l) {
        return this.translateOffset(l.endExclusive).lineNumber - this.translateOffset(l.start).lineNumber;
      }

      isStronglyEqual(l, u) {
        return this.elements[l] === this.elements[u];
      }

      extendToFullLines(l) {
        var u,
          b;
        const f = (u = (0, M.findLastMonotonous)(this.firstCharOffsetByLine, w => w <= l.start)) !== null && u !== void 0 ? u : 0,
          y = (b = (0, M.findFirstMonotonous)(this.firstCharOffsetByLine, w => l.endExclusive <= w)) !== null && b !== void 0 ? b : this.elements.length;
        return new R.OffsetRange(f, y);
      }
    }

    t.LinesSliceCharSequence = p;

    function c(s) {
      return s >= 97 && s <= 122 || s >= 65 && s <= 90 || s >= 48 && s <= 57;
    }

    const o = {
      0: 0,
      1: 0,
      2: 0,
      3: 10,
      4: 2,
      5: 30,
      6: 3,
      7: 10,
      8: 10,
    };

    function L(s) {
      return o[s];
    }

    function e(s) {
      return s === 10 ? 8 : s === 13 ? 7 : (0, _.isSpace)(s) ? 6 : s >= 97 && s <= 122 ? 0 : s >= 65 && s <= 90 ? 1 : s >= 48 && s <= 57 ? 2 : s === -1 ? 3 : s === 44 || s === 59 ? 5 : 4;
    }
  }), X(J[31], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.MovedText = t.LinesDiff = void 0;

    class M {
      constructor(d, _, p) {
        this.changes = d, this.moves = _, this.hitTimeout = p;
      }
    }

    t.LinesDiff = M;

    class R {
      constructor(d, _) {
        this.lineRangeMapping = d, this.changes = _;
      }
    }

    t.MovedText = R;
  }), X(J[16], Z([0, 1, 10]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.RangeMapping = t.DetailedLineRangeMapping = t.LineRangeMapping = void 0;

    class R {
      constructor(p, c) {
        this.original = p, this.modified = c;
      }

      static inverse(p, c, o) {
        const L = [];
        let e = 1,
          s = 1;
        for (const u of p) {
          const b = new R(new M.LineRange(e, u.original.startLineNumber), new M.LineRange(s, u.modified.startLineNumber));
          b.modified.isEmpty || L.push(b), e = u.original.endLineNumberExclusive, s = u.modified.endLineNumberExclusive;
        }
        const l = new R(new M.LineRange(e, c + 1), new M.LineRange(s, o + 1));
        return l.modified.isEmpty || L.push(l), L;
      }

      static clip(p, c, o) {
        const L = [];
        for (const e of p) {
          const s = e.original.intersect(c),
            l = e.modified.intersect(o);
          s && !s.isEmpty && l && !l.isEmpty && L.push(new R(s, l));
        }
        return L;
      }

      toString() {
        return `{${this.original.toString()}->${this.modified.toString()}}`;
      }

      flip() {
        return new R(this.modified, this.original);
      }

      join(p) {
        return new R(this.original.join(p.original), this.modified.join(p.modified));
      }
    }

    t.LineRangeMapping = R;

    class i extends R {
      constructor(p, c, o) {
        super(p, c), this.innerChanges = o;
      }

      flip() {
        var p;
        return new i(this.modified, this.original, (p = this.innerChanges) === null || p === void 0 ? void 0 : p.map(c => c.flip()));
      }
    }

    t.DetailedLineRangeMapping = i;

    class d {
      constructor(p, c) {
        this.originalRange = p, this.modifiedRange = c;
      }

      toString() {
        return `{${this.originalRange.toString()}->${this.modifiedRange.toString()}}`;
      }

      flip() {
        return new d(this.modifiedRange, this.originalRange);
      }
    }

    t.RangeMapping = d;
  }), X(J[46], Z([0, 1, 8, 16, 7, 11, 37, 10, 3, 30, 15, 29]), function(q, t, M, R, i, d, _, p, c, o, L, e) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.computeMovedLines = void 0;

    function s(E, S, C, r, a, g) {
      let {
        moves: m,
        excludedChanges: h,
      } = u(E, S, C, g);
      if (!g.isValid()) return [];
      const v = E.filter(A => !h.has(A)),
        N = b(v, r, a, S, C, g);
      return (0, i.pushMany)(m, N), m = y(m), m = m.filter(A => {
        const D = A.original.toOffsetRange()
          .slice(S)
          .map(T => T.trim());
        return D.join(`
`).length >= 15 && l(D, T => T.length >= 2) >= 2;
      }), m = w(E, m), m;
    }

    t.computeMovedLines = s;

    function l(E, S) {
      let C = 0;
      for (const r of E) S(r) && C++;
      return C;
    }

    function u(E, S, C, r) {
      const a = [],
        g = E.filter(v => v.modified.isEmpty && v.original.length >= 3)
          .map(v => new L.LineRangeFragment(v.original, S, v)),
        m = new Set(E.filter(v => v.original.isEmpty && v.modified.length >= 3)
          .map(v => new L.LineRangeFragment(v.modified, C, v))),
        h = new Set;
      for (const v of g) {
        let N = -1,
          A;
        for (const D of m) {
          const P = v.computeSimilarity(D);
          P > N && (N = P, A = D);
        }
        if (N > .9 && A && (m.delete(A), a.push(new R.LineRangeMapping(v.range, A.range)), h.add(v.source), h.add(A.source)), !r.isValid()) {
          return {
            moves: a,
            excludedChanges: h,
          };
        }
      }
      return {
        moves: a,
        excludedChanges: h,
      };
    }

    function b(E, S, C, r, a, g) {
      const m = [],
        h = new _.SetMap;
      for (const P of E) {
        for (let T = P.original.startLineNumber; T < P.original.endLineNumberExclusive - 2; T++) {
          const I = `${S[T - 1]}:${S[T + 1 - 1]}:${S[T + 2 - 1]}`;
          h.add(I, { range: new p.LineRange(T, T + 3) });
        }
      }
      const v = [];
      E.sort((0, i.compareBy)(P => P.modified.startLineNumber, i.numberComparator));
      for (const P of E) {
        let T = [];
        for (let I = P.modified.startLineNumber; I < P.modified.endLineNumberExclusive - 2; I++) {
          const B = `${C[I - 1]}:${C[I + 1 - 1]}:${C[I + 2 - 1]}`,
            z = new p.LineRange(I, I + 3),
            x = [];
          h.forEach(B, ({ range: O }) => {
            for (const W of T) {
              if (W.originalLineRange.endLineNumberExclusive + 1 === O.endLineNumberExclusive && W.modifiedLineRange.endLineNumberExclusive + 1 === z.endLineNumberExclusive) {
                W.originalLineRange = new p.LineRange(W.originalLineRange.startLineNumber, O.endLineNumberExclusive), W.modifiedLineRange = new p.LineRange(W.modifiedLineRange.startLineNumber, z.endLineNumberExclusive), x.push(W);
                return;
              }
            }
            const F = {
              modifiedLineRange: z,
              originalLineRange: O,
            };
            v.push(F), x.push(F);
          }), T = x;
        }
        if (!g.isValid()) return [];
      }
      v.sort((0, i.reverseOrder)((0, i.compareBy)(P => P.modifiedLineRange.length, i.numberComparator)));
      const N = new p.LineRangeSet,
        A = new p.LineRangeSet;
      for (const P of v) {
        const T = P.modifiedLineRange.startLineNumber - P.originalLineRange.startLineNumber,
          I = N.subtractFrom(P.modifiedLineRange),
          B = A.subtractFrom(P.originalLineRange)
            .getWithDelta(T),
          z = I.getIntersection(B);
        for (const x of z.ranges) {
          if (x.length < 3) continue;
          const O = x,
            F = x.delta(-T);
          m.push(new R.LineRangeMapping(F, O)), N.addRange(O), A.addRange(F);
        }
      }
      m.sort((0, i.compareBy)(P => P.original.startLineNumber, i.numberComparator));
      const D = new d.MonotonousArray(E);
      for (let P = 0; P < m.length; P++) {
        const T = m[P],
          I = D.findLastMonotonous(G => G.original.startLineNumber <= T.original.startLineNumber),
          B = (0, d.findLastMonotonous)(E, G => G.modified.startLineNumber <= T.modified.startLineNumber),
          z = Math.max(T.original.startLineNumber - I.original.startLineNumber, T.modified.startLineNumber - B.modified.startLineNumber),
          x = D.findLastMonotonous(G => G.original.startLineNumber < T.original.endLineNumberExclusive),
          O = (0, d.findLastMonotonous)(E, G => G.modified.startLineNumber < T.modified.endLineNumberExclusive),
          F = Math.max(x.original.endLineNumberExclusive - T.original.endLineNumberExclusive, O.modified.endLineNumberExclusive - T.modified.endLineNumberExclusive);
        let W;
        for (W = 0; W < z; W++) {
          const G = T.original.startLineNumber - W - 1,
            ne = T.modified.startLineNumber - W - 1;
          if (G > r.length || ne > a.length || N.contains(ne) || A.contains(G) || !f(r[G - 1], a[ne - 1], g)) break;
        }
        W > 0 && (A.addRange(new p.LineRange(T.original.startLineNumber - W, T.original.startLineNumber)), N.addRange(new p.LineRange(T.modified.startLineNumber - W, T.modified.startLineNumber)));
        let H;
        for (H = 0; H < F; H++) {
          const G = T.original.endLineNumberExclusive + H,
            ne = T.modified.endLineNumberExclusive + H;
          if (G > r.length || ne > a.length || N.contains(ne) || A.contains(G) || !f(r[G - 1], a[ne - 1], g)) break;
        }
        H > 0 && (A.addRange(new p.LineRange(T.original.endLineNumberExclusive, T.original.endLineNumberExclusive + H)), N.addRange(new p.LineRange(T.modified.endLineNumberExclusive, T.modified.endLineNumberExclusive + H))), (W > 0 || H > 0) && (m[P] = new R.LineRangeMapping(new p.LineRange(T.original.startLineNumber - W, T.original.endLineNumberExclusive + H), new p.LineRange(T.modified.startLineNumber - W, T.modified.endLineNumberExclusive + H)));
      }
      return m;
    }

    function f(E, S, C) {
      if (E.trim() === S.trim()) return !0;
      if (E.length > 300 && S.length > 300) return !1;
      const a = new e.MyersDiffAlgorithm().compute(new o.LinesSliceCharSequence([E], new c.OffsetRange(0, 1), !1), new o.LinesSliceCharSequence([S], new c.OffsetRange(0, 1), !1), C);
      let g = 0;
      const m = M.SequenceDiff.invert(a.diffs, E.length);
      for (const A of m) {
        A.seq1Range.forEach(D => {
          (0, L.isSpace)(E.charCodeAt(D)) || g++;
        });
      }

      function h(A) {
        let D = 0;
        for (let P = 0; P < E.length; P++) (0, L.isSpace)(A.charCodeAt(P)) || D++;
        return D;
      }

      const v = h(E.length > S.length ? E : S);
      return g / v > .6 && v > 10;
    }

    function y(E) {
      if (E.length === 0) return E;
      E.sort((0, i.compareBy)(C => C.original.startLineNumber, i.numberComparator));
      const S = [E[0]];
      for (let C = 1; C < E.length; C++) {
        const r = S[S.length - 1],
          a = E[C],
          g = a.original.startLineNumber - r.original.endLineNumberExclusive,
          m = a.modified.startLineNumber - r.modified.endLineNumberExclusive;
        if (g >= 0 && m >= 0 && g + m <= 2) {
          S[S.length - 1] = r.join(a);
          continue;
        }
        S.push(a);
      }
      return S;
    }

    function w(E, S) {
      const C = new d.MonotonousArray(E);
      return S = S.filter(r => {
        const a = C.findLastMonotonous(h => h.original.startLineNumber < r.original.endLineNumberExclusive) || new R.LineRangeMapping(new p.LineRange(1, 1), new p.LineRange(1, 1)),
          g = (0, d.findLastMonotonous)(E, h => h.modified.startLineNumber < r.modified.endLineNumberExclusive);
        return a !== g;
      }), S;
    }
  }), X(J[47], Z([0, 1, 7, 12, 10, 3, 2, 8, 45, 29, 46, 43, 31, 16, 30, 44]), function(q, t, M, R, i, d, _, p, c, o, L, e, s, l, u, b) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.getLineRangeMapping = t.lineRangeMappingFromRangeMappings = t.DefaultLinesDiffComputer = void 0;

    class f {
      constructor() {
        this.dynamicProgrammingDiffing = new c.DynamicProgrammingDiffing, this.myersDiffingAlgorithm = new o.MyersDiffAlgorithm;
      }

      computeDiff(S, C, r) {
        if (S.length <= 1 && (0, M.equals)(S, C, (H, G) => H === G)) return new s.LinesDiff([], [], !1);
        if (S.length === 1 && S[0].length === 0 || C.length === 1 && C[0].length === 0) return new s.LinesDiff([new l.DetailedLineRangeMapping(new i.LineRange(1, S.length + 1), new i.LineRange(1, C.length + 1), [new l.RangeMapping(new _.Range(1, 1, S.length, S[0].length + 1), new _.Range(1, 1, C.length, C[0].length + 1))])], [], !1);
        const a = r.maxComputationTimeMs === 0 ? p.InfiniteTimeout.instance : new p.DateTimeout(r.maxComputationTimeMs),
          g = !r.ignoreTrimWhitespace,
          m = new Map;

        function h(H) {
          let G = m.get(H);
          return G === void 0 && (G = m.size, m.set(H, G)), G;
        }

        const v = S.map(H => h(H.trim())),
          N = C.map(H => h(H.trim())),
          A = new b.LineSequence(v, S),
          D = new b.LineSequence(N, C),
          P = A.length + D.length < 1700 ? this.dynamicProgrammingDiffing.compute(A, D, a, (H, G) => S[H] === C[G] ? C[G].length === 0 ? .1 : 1 + Math.log(1 + C[G].length) : .99) : this.myersDiffingAlgorithm.compute(A, D);
        let T = P.diffs,
          I = P.hitTimeout;
        T = (0, e.optimizeSequenceDiffs)(A, D, T), T = (0, e.removeVeryShortMatchingLinesBetweenDiffs)(A, D, T);
        const B = [],
          z = H => {
            if (g) {
              for (let G = 0; G < H; G++) {
                const ne = x + G,
                  se = O + G;
                if (S[ne] !== C[se]) {
                  const n = this.refineDiff(S, C, new p.SequenceDiff(new d.OffsetRange(ne, ne + 1), new d.OffsetRange(se, se + 1)), a, g);
                  for (const be of n.mappings) B.push(be);
                  n.hitTimeout && (I = !0);
                }
              }
            }
          };
        let x = 0,
          O = 0;
        for (const H of T) {
          (0, R.assertFn)(() => H.seq1Range.start - x === H.seq2Range.start - O);
          const G = H.seq1Range.start - x;
          z(G), x = H.seq1Range.endExclusive, O = H.seq2Range.endExclusive;
          const ne = this.refineDiff(S, C, H, a, g);
          ne.hitTimeout && (I = !0);
          for (const se of ne.mappings) B.push(se);
        }
        z(S.length - x);
        const F = y(B, S, C);
        let W = [];
        return r.computeMoves && (W = this.computeMoves(F, S, C, v, N, a, g)), (0, R.assertFn)(() => {
          function H(ne, se) {
            if (ne.lineNumber < 1 || ne.lineNumber > se.length) return !1;
            const n = se[ne.lineNumber - 1];
            return !(ne.column < 1 || ne.column > n.length + 1);
          }

          function G(ne, se) {
            return !(ne.startLineNumber < 1 || ne.startLineNumber > se.length + 1 || ne.endLineNumberExclusive < 1 || ne.endLineNumberExclusive > se.length + 1);
          }

          for (const ne of F) {
            if (!ne.innerChanges) return !1;
            for (const se of ne.innerChanges) if (!(H(se.modifiedRange.getStartPosition(), C) && H(se.modifiedRange.getEndPosition(), C) && H(se.originalRange.getStartPosition(), S) && H(se.originalRange.getEndPosition(), S))) return !1;
            if (!G(ne.modified, C) || !G(ne.original, S)) return !1;
          }
          return !0;
        }), new s.LinesDiff(F, W, I);
      }

      computeMoves(S, C, r, a, g, m, h) {
        return (0, L.computeMovedLines)(S, C, r, a, g, m)
          .map(A => {
            const D = this.refineDiff(C, r, new p.SequenceDiff(A.original.toOffsetRange(), A.modified.toOffsetRange()), m, h),
              P = y(D.mappings, C, r, !0);
            return new s.MovedText(A, P);
          });
      }

      refineDiff(S, C, r, a, g) {
        const m = new u.LinesSliceCharSequence(S, r.seq1Range, g),
          h = new u.LinesSliceCharSequence(C, r.seq2Range, g),
          v = m.length + h.length < 500 ? this.dynamicProgrammingDiffing.compute(m, h, a) : this.myersDiffingAlgorithm.compute(m, h, a);
        let N = v.diffs;
        return N = (0, e.optimizeSequenceDiffs)(m, h, N), N = (0, e.extendDiffsToEntireWordIfAppropriate)(m, h, N), N = (0, e.removeShortMatches)(m, h, N), N = (0, e.removeVeryShortMatchingTextBetweenLongDiffs)(m, h, N), {
          mappings: N.map(D => new l.RangeMapping(m.translateRange(D.seq1Range), h.translateRange(D.seq2Range))),
          hitTimeout: v.hitTimeout,
        };
      }
    }

    t.DefaultLinesDiffComputer = f;

    function y(E, S, C, r = !1) {
      const a = [];
      for (const g of (0, M.groupAdjacentBy)(E.map(m => w(m, S, C)), (m, h) => m.original.overlapOrTouch(h.original) || m.modified.overlapOrTouch(h.modified))) {
        const m = g[0],
          h = g[g.length - 1];
        a.push(new l.DetailedLineRangeMapping(m.original.join(h.original), m.modified.join(h.modified), g.map(v => v.innerChanges[0])));
      }
      return (0, R.assertFn)(() => !r && a.length > 0 && a[0].original.startLineNumber !== a[0].modified.startLineNumber ? !1 : (0, R.checkAdjacentItems)(a, (g, m) => m.original.startLineNumber - g.original.endLineNumberExclusive === m.modified.startLineNumber - g.modified.endLineNumberExclusive && g.original.endLineNumberExclusive < m.original.startLineNumber && g.modified.endLineNumberExclusive < m.modified.startLineNumber)), a;
    }

    t.lineRangeMappingFromRangeMappings = y;

    function w(E, S, C) {
      let r = 0,
        a = 0;
      E.modifiedRange.endColumn === 1 && E.originalRange.endColumn === 1 && E.originalRange.startLineNumber + r <= E.originalRange.endLineNumber && E.modifiedRange.startLineNumber + r <= E.modifiedRange.endLineNumber && (a = -1), E.modifiedRange.startColumn - 1 >= C[E.modifiedRange.startLineNumber - 1].length && E.originalRange.startColumn - 1 >= S[E.originalRange.startLineNumber - 1].length && E.originalRange.startLineNumber <= E.originalRange.endLineNumber + a && E.modifiedRange.startLineNumber <= E.modifiedRange.endLineNumber + a && (r = 1);
      const g = new i.LineRange(E.originalRange.startLineNumber + r, E.originalRange.endLineNumber + 1 + a),
        m = new i.LineRange(E.modifiedRange.startLineNumber + r, E.modifiedRange.endLineNumber + 1 + a);
      return new l.DetailedLineRangeMapping(g, m, [E]);
    }

    t.getLineRangeMapping = w;
  }), X(J[48], Z([0, 1, 24, 31, 16, 6, 2, 12, 10]), function(q, t, M, R, i, d, _, p, c) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.DiffComputer = t.LegacyLinesDiffComputer = void 0;
    const o = 3;

    class L {
      computeDiff(r, a, g) {
        var m;
        const v = new y(r, a, {
            maxComputationTime: g.maxComputationTimeMs,
            shouldIgnoreTrimWhitespace: g.ignoreTrimWhitespace,
            shouldComputeCharChanges: !0,
            shouldMakePrettyDiff: !0,
            shouldPostProcessCharChanges: !0,
          }).computeDiff(),
          N = [];
        let A = null;
        for (const D of v.changes) {
          let P;
          D.originalEndLineNumber === 0 ? P = new c.LineRange(D.originalStartLineNumber + 1, D.originalStartLineNumber + 1) : P = new c.LineRange(D.originalStartLineNumber, D.originalEndLineNumber + 1);
          let T;
          D.modifiedEndLineNumber === 0 ? T = new c.LineRange(D.modifiedStartLineNumber + 1, D.modifiedStartLineNumber + 1) : T = new c.LineRange(D.modifiedStartLineNumber, D.modifiedEndLineNumber + 1);
          let I = new i.DetailedLineRangeMapping(P, T, (m = D.charChanges) === null || m === void 0 ? void 0 : m.map(B => new i.RangeMapping(new _.Range(B.originalStartLineNumber, B.originalStartColumn, B.originalEndLineNumber, B.originalEndColumn), new _.Range(B.modifiedStartLineNumber, B.modifiedStartColumn, B.modifiedEndLineNumber, B.modifiedEndColumn))));
          A && (A.modified.endLineNumberExclusive === I.modified.startLineNumber || A.original.endLineNumberExclusive === I.original.startLineNumber) && (I = new i.DetailedLineRangeMapping(A.original.join(I.original), A.modified.join(I.modified), A.innerChanges && I.innerChanges ? A.innerChanges.concat(I.innerChanges) : void 0), N.pop()), N.push(I), A = I;
        }
        return (0, p.assertFn)(() => (0, p.checkAdjacentItems)(N, (D, P) => P.original.startLineNumber - D.original.endLineNumberExclusive === P.modified.startLineNumber - D.modified.endLineNumberExclusive && D.original.endLineNumberExclusive < P.original.startLineNumber && D.modified.endLineNumberExclusive < P.modified.startLineNumber)), new R.LinesDiff(N, [], v.quitEarly);
      }
    }

    t.LegacyLinesDiffComputer = L;

    function e(C, r, a, g) {
      return new M.LcsDiff(C, r, a).ComputeDiff(g);
    }

    class s {
      constructor(r) {
        const a = [],
          g = [];
        for (let m = 0, h = r.length; m < h; m++) a[m] = w(r[m], 1), g[m] = E(r[m], 1);
        this.lines = r, this._startColumns = a, this._endColumns = g;
      }

      getElements() {
        const r = [];
        for (let a = 0, g = this.lines.length; a < g; a++) r[a] = this.lines[a].substring(this._startColumns[a] - 1, this._endColumns[a] - 1);
        return r;
      }

      getStrictElement(r) {
        return this.lines[r];
      }

      getStartLineNumber(r) {
        return r + 1;
      }

      getEndLineNumber(r) {
        return r + 1;
      }

      createCharSequence(r, a, g) {
        const m = [],
          h = [],
          v = [];
        let N = 0;
        for (let A = a; A <= g; A++) {
          const D = this.lines[A],
            P = r ? this._startColumns[A] : 1,
            T = r ? this._endColumns[A] : D.length + 1;
          for (let I = P; I < T; I++) m[N] = D.charCodeAt(I - 1), h[N] = A + 1, v[N] = I, N++;
          !r && A < g && (m[N] = 10, h[N] = A + 1, v[N] = D.length + 1, N++);
        }
        return new l(m, h, v);
      }
    }

    class l {
      constructor(r, a, g) {
        this._charCodes = r, this._lineNumbers = a, this._columns = g;
      }

      toString() {
        return '[' + this._charCodes.map((r, a) => (r === 10 ? '\\n' : String.fromCharCode(r)) + `-(${this._lineNumbers[a]},${this._columns[a]})`)
          .join(', ') + ']';
      }

      _assertIndex(r, a) {
        if (r < 0 || r >= a.length) throw new Error('Illegal index');
      }

      getElements() {
        return this._charCodes;
      }

      getStartLineNumber(r) {
        return r > 0 && r === this._lineNumbers.length ? this.getEndLineNumber(r - 1) : (this._assertIndex(r, this._lineNumbers), this._lineNumbers[r]);
      }

      getEndLineNumber(r) {
        return r === -1 ? this.getStartLineNumber(r + 1) : (this._assertIndex(r, this._lineNumbers), this._charCodes[r] === 10 ? this._lineNumbers[r] + 1 : this._lineNumbers[r]);
      }

      getStartColumn(r) {
        return r > 0 && r === this._columns.length ? this.getEndColumn(r - 1) : (this._assertIndex(r, this._columns), this._columns[r]);
      }

      getEndColumn(r) {
        return r === -1 ? this.getStartColumn(r + 1) : (this._assertIndex(r, this._columns), this._charCodes[r] === 10 ? 1 : this._columns[r] + 1);
      }
    }

    class u {
      constructor(r, a, g, m, h, v, N, A) {
        this.originalStartLineNumber = r, this.originalStartColumn = a, this.originalEndLineNumber = g, this.originalEndColumn = m, this.modifiedStartLineNumber = h, this.modifiedStartColumn = v, this.modifiedEndLineNumber = N, this.modifiedEndColumn = A;
      }

      static createFromDiffChange(r, a, g) {
        const m = a.getStartLineNumber(r.originalStart),
          h = a.getStartColumn(r.originalStart),
          v = a.getEndLineNumber(r.originalStart + r.originalLength - 1),
          N = a.getEndColumn(r.originalStart + r.originalLength - 1),
          A = g.getStartLineNumber(r.modifiedStart),
          D = g.getStartColumn(r.modifiedStart),
          P = g.getEndLineNumber(r.modifiedStart + r.modifiedLength - 1),
          T = g.getEndColumn(r.modifiedStart + r.modifiedLength - 1);
        return new u(m, h, v, N, A, D, P, T);
      }
    }

    function b(C) {
      if (C.length <= 1) return C;
      const r = [C[0]];
      let a = r[0];
      for (let g = 1, m = C.length; g < m; g++) {
        const h = C[g],
          v = h.originalStart - (a.originalStart + a.originalLength),
          N = h.modifiedStart - (a.modifiedStart + a.modifiedLength);
        Math.min(v, N) < o ? (a.originalLength = h.originalStart + h.originalLength - a.originalStart, a.modifiedLength = h.modifiedStart + h.modifiedLength - a.modifiedStart) : (r.push(h), a = h);
      }
      return r;
    }

    class f {
      constructor(r, a, g, m, h) {
        this.originalStartLineNumber = r, this.originalEndLineNumber = a, this.modifiedStartLineNumber = g, this.modifiedEndLineNumber = m, this.charChanges = h;
      }

      static createFromDiffResult(r, a, g, m, h, v, N) {
        let A,
          D,
          P,
          T,
          I;
        if (a.originalLength === 0 ? (A = g.getStartLineNumber(a.originalStart) - 1, D = 0) : (A = g.getStartLineNumber(a.originalStart), D = g.getEndLineNumber(a.originalStart + a.originalLength - 1)), a.modifiedLength === 0 ? (P = m.getStartLineNumber(a.modifiedStart) - 1, T = 0) : (P = m.getStartLineNumber(a.modifiedStart), T = m.getEndLineNumber(a.modifiedStart + a.modifiedLength - 1)), v && a.originalLength > 0 && a.originalLength < 20 && a.modifiedLength > 0 && a.modifiedLength < 20 && h()) {
          const B = g.createCharSequence(r, a.originalStart, a.originalStart + a.originalLength - 1),
            z = m.createCharSequence(r, a.modifiedStart, a.modifiedStart + a.modifiedLength - 1);
          if (B.getElements().length > 0 && z.getElements().length > 0) {
            let x = e(B, z, h, !0).changes;
            N && (x = b(x)), I = [];
            for (let O = 0, F = x.length; O < F; O++) I.push(u.createFromDiffChange(x[O], B, z));
          }
        }
        return new f(A, D, P, T, I);
      }
    }

    class y {
      constructor(r, a, g) {
        this.shouldComputeCharChanges = g.shouldComputeCharChanges, this.shouldPostProcessCharChanges = g.shouldPostProcessCharChanges, this.shouldIgnoreTrimWhitespace = g.shouldIgnoreTrimWhitespace, this.shouldMakePrettyDiff = g.shouldMakePrettyDiff, this.originalLines = r, this.modifiedLines = a, this.original = new s(r), this.modified = new s(a), this.continueLineDiff = S(g.maxComputationTime), this.continueCharDiff = S(g.maxComputationTime === 0 ? 0 : Math.min(g.maxComputationTime, 5e3));
      }

      computeDiff() {
        if (this.original.lines.length === 1 && this.original.lines[0].length === 0) {
          return this.modified.lines.length === 1 && this.modified.lines[0].length === 0 ? {
            quitEarly: !1,
            changes: [],
          } : {
            quitEarly: !1,
            changes: [{
              originalStartLineNumber: 1,
              originalEndLineNumber: 1,
              modifiedStartLineNumber: 1,
              modifiedEndLineNumber: this.modified.lines.length,
              charChanges: void 0,
            }],
          };
        }
        if (this.modified.lines.length === 1 && this.modified.lines[0].length === 0) {
          return {
            quitEarly: !1,
            changes: [{
              originalStartLineNumber: 1,
              originalEndLineNumber: this.original.lines.length,
              modifiedStartLineNumber: 1,
              modifiedEndLineNumber: 1,
              charChanges: void 0,
            }],
          };
        }
        const r = e(this.original, this.modified, this.continueLineDiff, this.shouldMakePrettyDiff),
          a = r.changes,
          g = r.quitEarly;
        if (this.shouldIgnoreTrimWhitespace) {
          const N = [];
          for (let A = 0, D = a.length; A < D; A++) N.push(f.createFromDiffResult(this.shouldIgnoreTrimWhitespace, a[A], this.original, this.modified, this.continueCharDiff, this.shouldComputeCharChanges, this.shouldPostProcessCharChanges));
          return {
            quitEarly: g,
            changes: N,
          };
        }
        const m = [];
        let h = 0,
          v = 0;
        for (let N = -1, A = a.length; N < A; N++) {
          const D = N + 1 < A ? a[N + 1] : null,
            P = D ? D.originalStart : this.originalLines.length,
            T = D ? D.modifiedStart : this.modifiedLines.length;
          for (; h < P && v < T;) {
            const I = this.originalLines[h],
              B = this.modifiedLines[v];
            if (I !== B) {
              {
                let z = w(I, 1),
                  x = w(B, 1);
                for (; z > 1 && x > 1;) {
                  const O = I.charCodeAt(z - 2),
                    F = B.charCodeAt(x - 2);
                  if (O !== F) break;
                  z--, x--;
                }
                (z > 1 || x > 1) && this._pushTrimWhitespaceCharChange(m, h + 1, 1, z, v + 1, 1, x);
              }
              {
                let z = E(I, 1),
                  x = E(B, 1);
                const O = I.length + 1,
                  F = B.length + 1;
                for (; z < O && x < F;) {
                  const W = I.charCodeAt(z - 1),
                    H = I.charCodeAt(x - 1);
                  if (W !== H) break;
                  z++, x++;
                }
                (z < O || x < F) && this._pushTrimWhitespaceCharChange(m, h + 1, z, O, v + 1, x, F);
              }
            }
            h++, v++;
          }
          D && (m.push(f.createFromDiffResult(this.shouldIgnoreTrimWhitespace, D, this.original, this.modified, this.continueCharDiff, this.shouldComputeCharChanges, this.shouldPostProcessCharChanges)), h += D.originalLength, v += D.modifiedLength);
        }
        return {
          quitEarly: g,
          changes: m,
        };
      }

      _pushTrimWhitespaceCharChange(r, a, g, m, h, v, N) {
        if (this._mergeTrimWhitespaceCharChange(r, a, g, m, h, v, N)) return;
        let A;
        this.shouldComputeCharChanges && (A = [new u(a, g, a, m, h, v, h, N)]), r.push(new f(a, a, h, h, A));
      }

      _mergeTrimWhitespaceCharChange(r, a, g, m, h, v, N) {
        const A = r.length;
        if (A === 0) return !1;
        const D = r[A - 1];
        return D.originalEndLineNumber === 0 || D.modifiedEndLineNumber === 0 ? !1 : D.originalEndLineNumber === a && D.modifiedEndLineNumber === h ? (this.shouldComputeCharChanges && D.charChanges && D.charChanges.push(new u(a, g, a, m, h, v, h, N)), !0) : D.originalEndLineNumber + 1 === a && D.modifiedEndLineNumber + 1 === h ? (D.originalEndLineNumber = a, D.modifiedEndLineNumber = h, this.shouldComputeCharChanges && D.charChanges && D.charChanges.push(new u(a, g, a, m, h, v, h, N)), !0) : !1;
      }
    }

    t.DiffComputer = y;

    function w(C, r) {
      const a = d.firstNonWhitespaceIndex(C);
      return a === -1 ? r : a + 1;
    }

    function E(C, r) {
      const a = d.lastNonWhitespaceIndex(C);
      return a === -1 ? r : a + 2;
    }

    function S(C) {
      if (C === 0) return () => !0;
      const r = Date.now();
      return () => Date.now() - r < C;
    }
  }), X(J[49], Z([0, 1, 48, 47]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.linesDiffComputers = void 0, t.linesDiffComputers = {
      getLegacy: () => new M.LegacyLinesDiffComputer,
      getDefault: () => new R.DefaultLinesDiffComputer,
    };
  }), X(J[50], Z([0, 1, 33]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.computeDefaultDocumentColors = void 0;

    function R(s) {
      const l = [];
      for (const u of s) {
        const b = Number(u);
        (b || b === 0 && u.replace(/\s/g, '') !== '') && l.push(b);
      }
      return l;
    }

    function i(s, l, u, b) {
      return {
        red: s / 255,
        blue: u / 255,
        green: l / 255,
        alpha: b,
      };
    }

    function d(s, l) {
      const u = l.index,
        b = l[0].length;
      if (!u) return;
      const f = s.positionAt(u);
      return {
        startLineNumber: f.lineNumber,
        startColumn: f.column,
        endLineNumber: f.lineNumber,
        endColumn: f.column + b,
      };
    }

    function _(s, l) {
      if (!s) return;
      const u = M.Color.Format.CSS.parseHex(l);
      if (u) {
        return {
          range: s,
          color: i(u.rgba.r, u.rgba.g, u.rgba.b, u.rgba.a),
        };
      }
    }

    function p(s, l, u) {
      if (!s || l.length !== 1) return;
      const f = l[0].values(),
        y = R(f);
      return {
        range: s,
        color: i(y[0], y[1], y[2], u ? y[3] : 1),
      };
    }

    function c(s, l, u) {
      if (!s || l.length !== 1) return;
      const f = l[0].values(),
        y = R(f),
        w = new M.Color(new M.HSLA(y[0], y[1] / 100, y[2] / 100, u ? y[3] : 1));
      return {
        range: s,
        color: i(w.rgba.r, w.rgba.g, w.rgba.b, w.rgba.a),
      };
    }

    function o(s, l) {
      return typeof s == 'string' ? [...s.matchAll(l)] : s.findMatches(l);
    }

    function L(s) {
      const l = [],
        b = o(s, /\b(rgb|rgba|hsl|hsla)(\([0-9\s,.\%]*\))|(#)([A-Fa-f0-9]{3})\b|(#)([A-Fa-f0-9]{4})\b|(#)([A-Fa-f0-9]{6})\b|(#)([A-Fa-f0-9]{8})\b/gm);
      if (b.length > 0) {
        for (const f of b) {
          const y = f.filter(C => C !== void 0),
            w = y[1],
            E = y[2];
          if (!E) continue;
          let S;
          if (w === 'rgb') {
            const C = /^\(\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*,\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*,\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*\)$/gm;
            S = p(d(s, f), o(E, C), !1);
          } else if (w === 'rgba') {
            const C = /^\(\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*,\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*,\s*(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])\s*,\s*(0[.][0-9]+|[.][0-9]+|[01][.]|[01])\s*\)$/gm;
            S = p(d(s, f), o(E, C), !0);
          } else if (w === 'hsl') {
            const C = /^\(\s*(36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])\s*,\s*(100|\d{1,2}[.]\d*|\d{1,2})%\s*,\s*(100|\d{1,2}[.]\d*|\d{1,2})%\s*\)$/gm;
            S = c(d(s, f), o(E, C), !1);
          } else if (w === 'hsla') {
            const C = /^\(\s*(36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])\s*,\s*(100|\d{1,2}[.]\d*|\d{1,2})%\s*,\s*(100|\d{1,2}[.]\d*|\d{1,2})%\s*,\s*(0[.][0-9]+|[.][0-9]+|[01][.]|[01])\s*\)$/gm;
            S = c(d(s, f), o(E, C), !0);
          } else {
            w === '#' && (S = _(d(s, f), w + E));
          }
          S && l.push(S);
        }
      }
      return l;
    }

    function e(s) {
      return !s || typeof s.getValue != 'function' || typeof s.positionAt != 'function' ? [] : L(s);
    }

    t.computeDefaultDocumentColors = e;
  }), X(J[51], Z([0, 1, 27]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.computeLinks = t.LinkComputer = t.StateMachine = void 0;

    class R {
      constructor(s, l, u) {
        const b = new Uint8Array(s * l);
        for (let f = 0, y = s * l; f < y; f++) b[f] = u;
        this._data = b, this.rows = s, this.cols = l;
      }

      get(s, l) {
        return this._data[s * this.cols + l];
      }

      set(s, l, u) {
        this._data[s * this.cols + l] = u;
      }
    }

    class i {
      constructor(s) {
        let l = 0,
          u = 0;
        for (let f = 0, y = s.length; f < y; f++) {
          const [w, E, S] = s[f];
          E > l && (l = E), w > u && (u = w), S > u && (u = S);
        }
        l++, u++;
        const b = new R(u, l, 0);
        for (let f = 0, y = s.length; f < y; f++) {
          const [w, E, S] = s[f];
          b.set(w, E, S);
        }
        this._states = b, this._maxCharCode = l;
      }

      nextState(s, l) {
        return l < 0 || l >= this._maxCharCode ? 0 : this._states.get(s, l);
      }
    }

    t.StateMachine = i;
    let d = null;

    function _() {
      return d === null && (d = new i([[1, 104, 2], [1, 72, 2], [1, 102, 6], [1, 70, 6], [2, 116, 3], [2, 84, 3], [3, 116, 4], [3, 84, 4], [4, 112, 5], [4, 80, 5], [5, 115, 9], [5, 83, 9], [5, 58, 10], [6, 105, 7], [6, 73, 7], [7, 108, 8], [7, 76, 8], [8, 101, 9], [8, 69, 9], [9, 58, 10], [10, 47, 11], [11, 47, 12]])), d;
    }

    let p = null;

    function c() {
      if (p === null) {
        p = new M.CharacterClassifier(0);
        const e = ` 	<>'"\u3001\u3002\uFF61\uFF64\uFF0C\uFF0E\uFF1A\uFF1B\u2018\u3008\u300C\u300E\u3014\uFF08\uFF3B\uFF5B\uFF62\uFF63\uFF5D\uFF3D\uFF09\u3015\u300F\u300D\u3009\u2019\uFF40\uFF5E\u2026`;
        for (let l = 0; l < e.length; l++) p.set(e.charCodeAt(l), 1);
        const s = '.,;:';
        for (let l = 0; l < s.length; l++) p.set(s.charCodeAt(l), 2);
      }
      return p;
    }

    class o {
      static _createLink(s, l, u, b, f) {
        let y = f - 1;
        do {
          const w = l.charCodeAt(y);
          if (s.get(w) !== 2) break;
          y--;
        } while (y > b);
        if (b > 0) {
          const w = l.charCodeAt(b - 1),
            E = l.charCodeAt(y);
          (w === 40 && E === 41 || w === 91 && E === 93 || w === 123 && E === 125) && y--;
        }
        return {
          range: {
            startLineNumber: u,
            startColumn: b + 1,
            endLineNumber: u,
            endColumn: y + 2,
          },
          url: l.substring(b, y + 1),
        };
      }

      static computeLinks(s, l = _()) {
        const u = c(),
          b = [];
        for (let f = 1, y = s.getLineCount(); f <= y; f++) {
          const w = s.getLineContent(f),
            E = w.length;
          let S = 0,
            C = 0,
            r = 0,
            a = 1,
            g = !1,
            m = !1,
            h = !1,
            v = !1;
          for (; S < E;) {
            let N = !1;
            const A = w.charCodeAt(S);
            if (a === 13) {
              let D;
              switch (A) {
                case 40:
                  g = !0, D = 0;
                  break;
                case 41:
                  D = g ? 0 : 1;
                  break;
                case 91:
                  h = !0, m = !0, D = 0;
                  break;
                case 93:
                  h = !1, D = m ? 0 : 1;
                  break;
                case 123:
                  v = !0, D = 0;
                  break;
                case 125:
                  D = v ? 0 : 1;
                  break;
                case 39:
                case 34:
                case 96:
                  r === A ? D = 1 : r === 39 || r === 34 || r === 96 ? D = 0 : D = 1;
                  break;
                case 42:
                  D = r === 42 ? 1 : 0;
                  break;
                case 124:
                  D = r === 124 ? 1 : 0;
                  break;
                case 32:
                  D = h ? 0 : 1;
                  break;
                default:
                  D = u.get(A);
              }
              D === 1 && (b.push(o._createLink(u, w, f, C, S)), N = !0);
            } else if (a === 12) {
              let D;
              A === 91 ? (m = !0, D = 0) : D = u.get(A), D === 1 ? N = !0 : a = 13;
            } else {
              a = l.nextState(a, A), a === 0 && (N = !0);
            }
            N && (a = 1, g = !1, m = !1, v = !1, C = S + 1, r = A), S++;
          }
          a === 13 && b.push(o._createLink(u, w, f, C, E));
        }
        return b;
      }
    }

    t.LinkComputer = o;

    function L(e) {
      return !e || typeof e.getLineCount != 'function' || typeof e.getLineContent != 'function' ? [] : o.computeLinks(e);
    }

    t.computeLinks = L;
  }), X(J[52], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.BasicInplaceReplace = void 0;

    class M {
      constructor() {
        this._defaultValueSet = [['true', 'false'], ['True', 'False'], ['Private', 'Public', 'Friend', 'ReadOnly', 'Partial', 'Protected', 'WriteOnly'], ['public', 'protected', 'private']];
      }

      navigateValueSet(i, d, _, p, c) {
        if (i && d) {
          const o = this.doNavigateValueSet(d, c);
          if (o) {
            return {
              range: i,
              value: o,
            };
          }
        }
        if (_ && p) {
          const o = this.doNavigateValueSet(p, c);
          if (o) {
            return {
              range: _,
              value: o,
            };
          }
        }
        return null;
      }

      doNavigateValueSet(i, d) {
        const _ = this.numberReplace(i, d);
        return _ !== null ? _ : this.textReplace(i, d);
      }

      numberReplace(i, d) {
        const _ = Math.pow(10, i.length - (i.lastIndexOf('.') + 1));
        let p = Number(i);
        const c = parseFloat(i);
        return !isNaN(p) && !isNaN(c) && p === c ? p === 0 && !d ? null : (p = Math.floor(p * _), p += d ? _ : -_, String(p / _)) : null;
      }

      textReplace(i, d) {
        return this.valueSetsReplace(this._defaultValueSet, i, d);
      }

      valueSetsReplace(i, d, _) {
        let p = null;
        for (let c = 0, o = i.length; p === null && c < o; c++) p = this.valueSetReplace(i[c], d, _);
        return p;
      }

      valueSetReplace(i, d, _) {
        let p = i.indexOf(d);
        return p >= 0 ? (p += _ ? 1 : -1, p < 0 ? p = i.length - 1 : p %= i.length, i[p]) : null;
      }
    }

    t.BasicInplaceReplace = M, M.INSTANCE = new M;
  }), X(J[53], Z([0, 1, 14]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.shouldSynchronizeModel = t.ApplyEditsResult = t.SearchData = t.ValidAnnotatedEditOperation = t.isITextSnapshot = t.FindMatch = t.TextModelResolvedOptions = t.InjectedTextCursorStops = t.MinimapPosition = t.GlyphMarginLane = t.OverviewRulerLane = void 0;
    var R;
    (function(u) {
      u[u.Left = 1] = 'Left', u[u.Center = 2] = 'Center', u[u.Right = 4] = 'Right', u[u.Full = 7] = 'Full';
    })(R || (t.OverviewRulerLane = R = {}));
    var i;
    (function(u) {
      u[u.Left = 1] = 'Left', u[u.Center = 2] = 'Center', u[u.Right = 3] = 'Right';
    })(i || (t.GlyphMarginLane = i = {}));
    var d;
    (function(u) {
      u[u.Inline = 1] = 'Inline', u[u.Gutter = 2] = 'Gutter';
    })(d || (t.MinimapPosition = d = {}));
    var _;
    (function(u) {
      u[u.Both = 0] = 'Both', u[u.Right = 1] = 'Right', u[u.Left = 2] = 'Left', u[u.None = 3] = 'None';
    })(_ || (t.InjectedTextCursorStops = _ = {}));

    class p {
      constructor(b) {
        this._textModelResolvedOptionsBrand = void 0, this.tabSize = Math.max(1, b.tabSize | 0), b.indentSize === 'tabSize' ? (this.indentSize = this.tabSize, this._indentSizeIsTabSize = !0) : (this.indentSize = Math.max(1, b.indentSize | 0), this._indentSizeIsTabSize = !1), this.insertSpaces = !!b.insertSpaces, this.defaultEOL = b.defaultEOL | 0, this.trimAutoWhitespace = !!b.trimAutoWhitespace, this.bracketPairColorizationOptions = b.bracketPairColorizationOptions;
      }

      get originalIndentSize() {
        return this._indentSizeIsTabSize ? 'tabSize' : this.indentSize;
      }

      equals(b) {
        return this.tabSize === b.tabSize && this._indentSizeIsTabSize === b._indentSizeIsTabSize && this.indentSize === b.indentSize && this.insertSpaces === b.insertSpaces && this.defaultEOL === b.defaultEOL && this.trimAutoWhitespace === b.trimAutoWhitespace && (0, M.equals)(this.bracketPairColorizationOptions, b.bracketPairColorizationOptions);
      }

      createChangeEvent(b) {
        return {
          tabSize: this.tabSize !== b.tabSize,
          indentSize: this.indentSize !== b.indentSize,
          insertSpaces: this.insertSpaces !== b.insertSpaces,
          trimAutoWhitespace: this.trimAutoWhitespace !== b.trimAutoWhitespace,
        };
      }
    }

    t.TextModelResolvedOptions = p;

    class c {
      constructor(b, f) {
        this._findMatchBrand = void 0, this.range = b, this.matches = f;
      }
    }

    t.FindMatch = c;

    function o(u) {
      return u && typeof u.read == 'function';
    }

    t.isITextSnapshot = o;

    class L {
      constructor(b, f, y, w, E, S) {
        this.identifier = b, this.range = f, this.text = y, this.forceMoveMarkers = w, this.isAutoWhitespaceEdit = E, this._isTracked = S;
      }
    }

    t.ValidAnnotatedEditOperation = L;

    class e {
      constructor(b, f, y) {
        this.regex = b, this.wordSeparators = f, this.simpleSearch = y;
      }
    }

    t.SearchData = e;

    class s {
      constructor(b, f, y) {
        this.reverseEdits = b, this.changes = f, this.trimAutoWhitespaceLineNumbers = y;
      }
    }

    t.ApplyEditsResult = s;

    function l(u) {
      return !u.isTooLargeForSyncing() && !u.isForSimpleWidget;
    }

    t.shouldSynchronizeModel = l;
  }), X(J[54], Z([0, 1, 7, 26]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.PrefixSumIndexOfResult = t.ConstantTimePrefixSumComputer = t.PrefixSumComputer = void 0;

    class i {
      constructor(c) {
        this.values = c, this.prefixSum = new Uint32Array(c.length), this.prefixSumValidIndex = new Int32Array(1), this.prefixSumValidIndex[0] = -1;
      }

      insertValues(c, o) {
        c = (0, R.toUint32)(c);
        const L = this.values,
          e = this.prefixSum,
          s = o.length;
        return s === 0 ? !1 : (this.values = new Uint32Array(L.length + s), this.values.set(L.subarray(0, c), 0), this.values.set(L.subarray(c), c + s), this.values.set(o, c), c - 1 < this.prefixSumValidIndex[0] && (this.prefixSumValidIndex[0] = c - 1), this.prefixSum = new Uint32Array(this.values.length), this.prefixSumValidIndex[0] >= 0 && this.prefixSum.set(e.subarray(0, this.prefixSumValidIndex[0] + 1)), !0);
      }

      setValue(c, o) {
        return c = (0, R.toUint32)(c), o = (0, R.toUint32)(o), this.values[c] === o ? !1 : (this.values[c] = o, c - 1 < this.prefixSumValidIndex[0] && (this.prefixSumValidIndex[0] = c - 1), !0);
      }

      removeValues(c, o) {
        c = (0, R.toUint32)(c), o = (0, R.toUint32)(o);
        const L = this.values,
          e = this.prefixSum;
        if (c >= L.length) return !1;
        const s = L.length - c;
        return o >= s && (o = s), o === 0 ? !1 : (this.values = new Uint32Array(L.length - o), this.values.set(L.subarray(0, c), 0), this.values.set(L.subarray(c + o), c), this.prefixSum = new Uint32Array(this.values.length), c - 1 < this.prefixSumValidIndex[0] && (this.prefixSumValidIndex[0] = c - 1), this.prefixSumValidIndex[0] >= 0 && this.prefixSum.set(e.subarray(0, this.prefixSumValidIndex[0] + 1)), !0);
      }

      getTotalSum() {
        return this.values.length === 0 ? 0 : this._getPrefixSum(this.values.length - 1);
      }

      getPrefixSum(c) {
        return c < 0 ? 0 : (c = (0, R.toUint32)(c), this._getPrefixSum(c));
      }

      _getPrefixSum(c) {
        if (c <= this.prefixSumValidIndex[0]) return this.prefixSum[c];
        let o = this.prefixSumValidIndex[0] + 1;
        o === 0 && (this.prefixSum[0] = this.values[0], o++), c >= this.values.length && (c = this.values.length - 1);
        for (let L = o; L <= c; L++) this.prefixSum[L] = this.prefixSum[L - 1] + this.values[L];
        return this.prefixSumValidIndex[0] = Math.max(this.prefixSumValidIndex[0], c), this.prefixSum[c];
      }

      getIndexOf(c) {
        c = Math.floor(c), this.getTotalSum();
        let o = 0,
          L = this.values.length - 1,
          e = 0,
          s = 0,
          l = 0;
        for (; o <= L;) if (e = o + (L - o) / 2 | 0, s = this.prefixSum[e], l = s - this.values[e], c < l) L = e - 1; else if (c >= s) o = e + 1; else break;
        return new _(e, c - l);
      }
    }

    t.PrefixSumComputer = i;

    class d {
      constructor(c) {
        this._values = c, this._isValid = !1, this._validEndIndex = -1, this._prefixSum = [], this._indexBySum = [];
      }

      getTotalSum() {
        return this._ensureValid(), this._indexBySum.length;
      }

      getPrefixSum(c) {
        return this._ensureValid(), c === 0 ? 0 : this._prefixSum[c - 1];
      }

      getIndexOf(c) {
        this._ensureValid();
        const o = this._indexBySum[c],
          L = o > 0 ? this._prefixSum[o - 1] : 0;
        return new _(o, c - L);
      }

      removeValues(c, o) {
        this._values.splice(c, o), this._invalidate(c);
      }

      insertValues(c, o) {
        this._values = (0, M.arrayInsert)(this._values, c, o), this._invalidate(c);
      }

      _invalidate(c) {
        this._isValid = !1, this._validEndIndex = Math.min(this._validEndIndex, c - 1);
      }

      _ensureValid() {
        if (!this._isValid) {
          for (let c = this._validEndIndex + 1, o = this._values.length; c < o; c++) {
            const L = this._values[c],
              e = c > 0 ? this._prefixSum[c - 1] : 0;
            this._prefixSum[c] = e + L;
            for (let s = 0; s < L; s++) this._indexBySum[e + s] = c;
          }
          this._prefixSum.length = this._values.length, this._indexBySum.length = this._prefixSum[this._prefixSum.length - 1], this._isValid = !0, this._validEndIndex = this._values.length - 1;
        }
      }

      setValue(c, o) {
        this._values[c] !== o && (this._values[c] = o, this._invalidate(c));
      }
    }

    t.ConstantTimePrefixSumComputer = d;

    class _ {
      constructor(c, o) {
        this.index = c, this.remainder = o, this._prefixSumIndexOfResultBrand = void 0, this.index = c, this.remainder = o;
      }
    }

    t.PrefixSumIndexOfResult = _;
  }), X(J[55], Z([0, 1, 6, 4, 54]), function(q, t, M, R, i) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.MirrorTextModel = void 0;

    class d {
      constructor(p, c, o, L) {
        this._uri = p, this._lines = c, this._eol = o, this._versionId = L, this._lineStarts = null, this._cachedTextValue = null;
      }

      get version() {
        return this._versionId;
      }

      dispose() {
        this._lines.length = 0;
      }

      getText() {
        return this._cachedTextValue === null && (this._cachedTextValue = this._lines.join(this._eol)), this._cachedTextValue;
      }

      onEvents(p) {
        p.eol && p.eol !== this._eol && (this._eol = p.eol, this._lineStarts = null);
        const c = p.changes;
        for (const o of c) this._acceptDeleteRange(o.range), this._acceptInsertText(new R.Position(o.range.startLineNumber, o.range.startColumn), o.text);
        this._versionId = p.versionId, this._cachedTextValue = null;
      }

      _ensureLineStarts() {
        if (!this._lineStarts) {
          const p = this._eol.length,
            c = this._lines.length,
            o = new Uint32Array(c);
          for (let L = 0; L < c; L++) o[L] = this._lines[L].length + p;
          this._lineStarts = new i.PrefixSumComputer(o);
        }
      }

      _setLineText(p, c) {
        this._lines[p] = c, this._lineStarts && this._lineStarts.setValue(p, this._lines[p].length + this._eol.length);
      }

      _acceptDeleteRange(p) {
        if (p.startLineNumber === p.endLineNumber) {
          if (p.startColumn === p.endColumn) return;
          this._setLineText(p.startLineNumber - 1, this._lines[p.startLineNumber - 1].substring(0, p.startColumn - 1) + this._lines[p.startLineNumber - 1].substring(p.endColumn - 1));
          return;
        }
        this._setLineText(p.startLineNumber - 1, this._lines[p.startLineNumber - 1].substring(0, p.startColumn - 1) + this._lines[p.endLineNumber - 1].substring(p.endColumn - 1)), this._lines.splice(p.startLineNumber, p.endLineNumber - p.startLineNumber), this._lineStarts && this._lineStarts.removeValues(p.startLineNumber, p.endLineNumber - p.startLineNumber);
      }

      _acceptInsertText(p, c) {
        if (c.length === 0) return;
        const o = (0, M.splitLines)(c);
        if (o.length === 1) {
          this._setLineText(p.lineNumber - 1, this._lines[p.lineNumber - 1].substring(0, p.column - 1) + o[0] + this._lines[p.lineNumber - 1].substring(p.column - 1));
          return;
        }
        o[o.length - 1] += this._lines[p.lineNumber - 1].substring(p.column - 1), this._setLineText(p.lineNumber - 1, this._lines[p.lineNumber - 1].substring(0, p.column - 1) + o[0]);
        const L = new Uint32Array(o.length - 1);
        for (let e = 1; e < o.length; e++) this._lines.splice(p.lineNumber + e - 1, 0, o[e]), L[e - 1] = o[e].length + this._eol.length;
        this._lineStarts && this._lineStarts.insertValues(p.lineNumber, L);
      }
    }

    t.MirrorTextModel = d;
  }), X(J[56], Z([0, 1, 6, 42, 4, 2, 53]), function(q, t, M, R, i, d, _) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.Searcher = t.isValidMatch = t.TextModelSearch = t.createFindMatch = t.isMultilineRegexSource = t.SearchParams = void 0;
    const p = 999;

    class c {
      constructor(w, E, S, C) {
        this.searchString = w, this.isRegex = E, this.matchCase = S, this.wordSeparators = C;
      }

      parseSearchRequest() {
        if (this.searchString === '') return null;
        let w;
        this.isRegex ? w = o(this.searchString) : w = this.searchString.indexOf(`
`) >= 0;
        let E = null;
        try {
          E = M.createRegExp(this.searchString, this.isRegex, {
            matchCase: this.matchCase,
            wholeWord: !1,
            multiline: w,
            global: !0,
            unicode: !0,
          });
        } catch {
          return null;
        }
        if (!E) return null;
        let S = !this.isRegex && !w;
        return S && this.searchString.toLowerCase() !== this.searchString.toUpperCase() && (S = this.matchCase), new _.SearchData(E, this.wordSeparators ? (0, R.getMapForWordSeparators)(this.wordSeparators) : null, S ? this.searchString : null);
      }
    }

    t.SearchParams = c;

    function o(y) {
      if (!y || y.length === 0) return !1;
      for (let w = 0, E = y.length; w < E; w++) {
        const S = y.charCodeAt(w);
        if (S === 10) return !0;
        if (S === 92) {
          if (w++, w >= E) break;
          const C = y.charCodeAt(w);
          if (C === 110 || C === 114 || C === 87) return !0;
        }
      }
      return !1;
    }

    t.isMultilineRegexSource = o;

    function L(y, w, E) {
      if (!E) return new _.FindMatch(y, null);
      const S = [];
      for (let C = 0, r = w.length; C < r; C++) S[C] = w[C];
      return new _.FindMatch(y, S);
    }

    t.createFindMatch = L;

    class e {
      constructor(w) {
        const E = [];
        let S = 0;
        for (let C = 0, r = w.length; C < r; C++) w.charCodeAt(C) === 10 && (E[S++] = C);
        this._lineFeedsOffsets = E;
      }

      findLineFeedCountBeforeOffset(w) {
        const E = this._lineFeedsOffsets;
        let S = 0,
          C = E.length - 1;
        if (C === -1 || w <= E[0]) return 0;
        for (; S < C;) {
          const r = S + ((C - S) / 2 >> 0);
          E[r] >= w ? C = r - 1 : E[r + 1] >= w ? (S = r, C = r) : S = r + 1;
        }
        return S + 1;
      }
    }

    class s {
      static findMatches(w, E, S, C, r) {
        const a = E.parseSearchRequest();
        return a ? a.regex.multiline ? this._doFindMatchesMultiline(w, S, new f(a.wordSeparators, a.regex), C, r) : this._doFindMatchesLineByLine(w, S, a, C, r) : [];
      }

      static _getMultilineMatchRange(w, E, S, C, r, a) {
        let g,
          m = 0;
        C ? (m = C.findLineFeedCountBeforeOffset(r), g = E + r + m) : g = E + r;
        let h;
        if (C) {
          const D = C.findLineFeedCountBeforeOffset(r + a.length) - m;
          h = g + a.length + D;
        } else {
          h = g + a.length;
        }
        const v = w.getPositionAt(g),
          N = w.getPositionAt(h);
        return new d.Range(v.lineNumber, v.column, N.lineNumber, N.column);
      }

      static _doFindMatchesMultiline(w, E, S, C, r) {
        const a = w.getOffsetAt(E.getStartPosition()),
          g = w.getValueInRange(E, 1),
          m = w.getEOL() === `\r
` ? new e(g) : null,
          h = [];
        let v = 0,
          N;
        for (S.reset(0); N = S.next(g);) if (h[v++] = L(this._getMultilineMatchRange(w, a, g, m, N.index, N[0]), N, C), v >= r) return h;
        return h;
      }

      static _doFindMatchesLineByLine(w, E, S, C, r) {
        const a = [];
        let g = 0;
        if (E.startLineNumber === E.endLineNumber) {
          const h = w.getLineContent(E.startLineNumber)
            .substring(E.startColumn - 1, E.endColumn - 1);
          return g = this._findMatchesInLine(S, h, E.startLineNumber, E.startColumn - 1, g, a, C, r), a;
        }
        const m = w.getLineContent(E.startLineNumber)
          .substring(E.startColumn - 1);
        g = this._findMatchesInLine(S, m, E.startLineNumber, E.startColumn - 1, g, a, C, r);
        for (let h = E.startLineNumber + 1; h < E.endLineNumber && g < r; h++) g = this._findMatchesInLine(S, w.getLineContent(h), h, 0, g, a, C, r);
        if (g < r) {
          const h = w.getLineContent(E.endLineNumber)
            .substring(0, E.endColumn - 1);
          g = this._findMatchesInLine(S, h, E.endLineNumber, 0, g, a, C, r);
        }
        return a;
      }

      static _findMatchesInLine(w, E, S, C, r, a, g, m) {
        const h = w.wordSeparators;
        if (!g && w.simpleSearch) {
          const A = w.simpleSearch,
            D = A.length,
            P = E.length;
          let T = -D;
          for (; (T = E.indexOf(A, T + D)) !== -1;) if ((!h || b(h, E, P, T, D)) && (a[r++] = new _.FindMatch(new d.Range(S, T + 1 + C, S, T + 1 + D + C), null), r >= m)) return r;
          return r;
        }
        const v = new f(w.wordSeparators, w.regex);
        let N;
        v.reset(0);
        do if (N = v.next(E), N && (a[r++] = L(new d.Range(S, N.index + 1 + C, S, N.index + 1 + N[0].length + C), N, g), r >= m)) return r; while (N);
        return r;
      }

      static findNextMatch(w, E, S, C) {
        const r = E.parseSearchRequest();
        if (!r) return null;
        const a = new f(r.wordSeparators, r.regex);
        return r.regex.multiline ? this._doFindNextMatchMultiline(w, S, a, C) : this._doFindNextMatchLineByLine(w, S, a, C);
      }

      static _doFindNextMatchMultiline(w, E, S, C) {
        const r = new i.Position(E.lineNumber, 1),
          a = w.getOffsetAt(r),
          g = w.getLineCount(),
          m = w.getValueInRange(new d.Range(r.lineNumber, r.column, g, w.getLineMaxColumn(g)), 1),
          h = w.getEOL() === `\r
` ? new e(m) : null;
        S.reset(E.column - 1);
        const v = S.next(m);
        return v ? L(this._getMultilineMatchRange(w, a, m, h, v.index, v[0]), v, C) : E.lineNumber !== 1 || E.column !== 1 ? this._doFindNextMatchMultiline(w, new i.Position(1, 1), S, C) : null;
      }

      static _doFindNextMatchLineByLine(w, E, S, C) {
        const r = w.getLineCount(),
          a = E.lineNumber,
          g = w.getLineContent(a),
          m = this._findFirstMatchInLine(S, g, a, E.column, C);
        if (m) return m;
        for (let h = 1; h <= r; h++) {
          const v = (a + h - 1) % r,
            N = w.getLineContent(v + 1),
            A = this._findFirstMatchInLine(S, N, v + 1, 1, C);
          if (A) return A;
        }
        return null;
      }

      static _findFirstMatchInLine(w, E, S, C, r) {
        w.reset(C - 1);
        const a = w.next(E);
        return a ? L(new d.Range(S, a.index + 1, S, a.index + 1 + a[0].length), a, r) : null;
      }

      static findPreviousMatch(w, E, S, C) {
        const r = E.parseSearchRequest();
        if (!r) return null;
        const a = new f(r.wordSeparators, r.regex);
        return r.regex.multiline ? this._doFindPreviousMatchMultiline(w, S, a, C) : this._doFindPreviousMatchLineByLine(w, S, a, C);
      }

      static _doFindPreviousMatchMultiline(w, E, S, C) {
        const r = this._doFindMatchesMultiline(w, new d.Range(1, 1, E.lineNumber, E.column), S, C, 10 * p);
        if (r.length > 0) return r[r.length - 1];
        const a = w.getLineCount();
        return E.lineNumber !== a || E.column !== w.getLineMaxColumn(a) ? this._doFindPreviousMatchMultiline(w, new i.Position(a, w.getLineMaxColumn(a)), S, C) : null;
      }

      static _doFindPreviousMatchLineByLine(w, E, S, C) {
        const r = w.getLineCount(),
          a = E.lineNumber,
          g = w.getLineContent(a)
            .substring(0, E.column - 1),
          m = this._findLastMatchInLine(S, g, a, C);
        if (m) return m;
        for (let h = 1; h <= r; h++) {
          const v = (r + a - h - 1) % r,
            N = w.getLineContent(v + 1),
            A = this._findLastMatchInLine(S, N, v + 1, C);
          if (A) return A;
        }
        return null;
      }

      static _findLastMatchInLine(w, E, S, C) {
        let r = null,
          a;
        for (w.reset(0); a = w.next(E);) r = L(new d.Range(S, a.index + 1, S, a.index + 1 + a[0].length), a, C);
        return r;
      }
    }

    t.TextModelSearch = s;

    function l(y, w, E, S, C) {
      if (S === 0) return !0;
      const r = w.charCodeAt(S - 1);
      if (y.get(r) !== 0 || r === 13 || r === 10) return !0;
      if (C > 0) {
        const a = w.charCodeAt(S);
        if (y.get(a) !== 0) return !0;
      }
      return !1;
    }

    function u(y, w, E, S, C) {
      if (S + C === E) return !0;
      const r = w.charCodeAt(S + C);
      if (y.get(r) !== 0 || r === 13 || r === 10) return !0;
      if (C > 0) {
        const a = w.charCodeAt(S + C - 1);
        if (y.get(a) !== 0) return !0;
      }
      return !1;
    }

    function b(y, w, E, S, C) {
      return l(y, w, E, S, C) && u(y, w, E, S, C);
    }

    t.isValidMatch = b;

    class f {
      constructor(w, E) {
        this._wordSeparators = w, this._searchRegex = E, this._prevMatchStartIndex = -1, this._prevMatchLength = 0;
      }

      reset(w) {
        this._searchRegex.lastIndex = w, this._prevMatchStartIndex = -1, this._prevMatchLength = 0;
      }

      next(w) {
        const E = w.length;
        let S;
        do {
          if (this._prevMatchStartIndex + this._prevMatchLength === E || (S = this._searchRegex.exec(w), !S)) return null;
          const C = S.index,
            r = S[0].length;
          if (C === this._prevMatchStartIndex && r === this._prevMatchLength) {
            if (r === 0) {
              M.getNextCodePoint(w, E, this._searchRegex.lastIndex) > 65535 ? this._searchRegex.lastIndex += 2 : this._searchRegex.lastIndex += 1;
              continue;
            }
            return null;
          }
          if (this._prevMatchStartIndex = C, this._prevMatchLength = r, !this._wordSeparators || b(this._wordSeparators, w, E, C, r)) return S;
        } while (S);
        return null;
      }
    }

    t.Searcher = f;
  }), X(J[57], Z([0, 1, 2, 56, 6, 12, 28]), function(q, t, M, R, i, d, _) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.UnicodeTextModelHighlighter = void 0;

    class p {
      static computeUnicodeHighlights(s, l, u) {
        const b = u ? u.startLineNumber : 1,
          f = u ? u.endLineNumber : s.getLineCount(),
          y = new o(l),
          w = y.getCandidateCodePoints();
        let E;
        w === 'allNonBasicAscii' ? E = new RegExp('[^\\t\\n\\r\\x20-\\x7E]', 'g') : E = new RegExp(`${c(Array.from(w))}`, 'g');
        const S = new R.Searcher(null, E),
          C = [];
        let r = !1,
          a,
          g = 0,
          m = 0,
          h = 0;
        e:for (let v = b, N = f; v <= N; v++) {
          const A = s.getLineContent(v),
            D = A.length;
          S.reset(0);
          do {
            if (a = S.next(A), a) {
              let P = a.index,
                T = a.index + a[0].length;
              if (P > 0) {
                const x = A.charCodeAt(P - 1);
                i.isHighSurrogate(x) && P--;
              }
              if (T + 1 < D) {
                const x = A.charCodeAt(T - 1);
                i.isHighSurrogate(x) && T++;
              }
              const I = A.substring(P, T);
              let B = (0, _.getWordAtText)(P + 1, _.DEFAULT_WORD_REGEXP, A, 0);
              B && B.endColumn <= P + 1 && (B = null);
              const z = y.shouldHighlightNonBasicASCII(I, B ? B.word : null);
              if (z !== 0) {
                if (z === 3 ? g++ : z === 2 ? m++ : z === 1 ? h++ : (0, d.assertNever)(z), C.length >= 1e3) {
                  r = !0;
                  break e;
                }
                C.push(new M.Range(v, P + 1, v, T + 1));
              }
            }
          } while (a);
        }
        return {
          ranges: C,
          hasMore: r,
          ambiguousCharacterCount: g,
          invisibleCharacterCount: m,
          nonBasicAsciiCharacterCount: h,
        };
      }

      static computeUnicodeHighlightReason(s, l) {
        const u = new o(l);
        switch (u.shouldHighlightNonBasicASCII(s, null)) {
          case 0:
            return null;
          case 2:
            return { kind: 1 };
          case 3: {
            const f = s.codePointAt(0),
              y = u.ambiguousCharacters.getPrimaryConfusable(f),
              w = i.AmbiguousCharacters.getLocales()
                .filter(E => !i.AmbiguousCharacters.getInstance(new Set([...l.allowedLocales, E]))
                  .isAmbiguous(f));
            return {
              kind: 0,
              confusableWith: String.fromCodePoint(y),
              notAmbiguousInLocales: w,
            };
          }
          case 1:
            return { kind: 2 };
        }
      }
    }

    t.UnicodeTextModelHighlighter = p;

    function c(e, s) {
      return `[${i.escapeRegExpCharacters(e.map(u => String.fromCodePoint(u))
        .join(''))}]`;
    }

    class o {
      constructor(s) {
        this.options = s, this.allowedCodePoints = new Set(s.allowedCodePoints), this.ambiguousCharacters = i.AmbiguousCharacters.getInstance(new Set(s.allowedLocales));
      }

      getCandidateCodePoints() {
        if (this.options.nonBasicASCII) return 'allNonBasicAscii';
        const s = new Set;
        if (this.options.invisibleCharacters) for (const l of i.InvisibleCharacters.codePoints) L(String.fromCodePoint(l)) || s.add(l);
        if (this.options.ambiguousCharacters) for (const l of this.ambiguousCharacters.getConfusableCodePoints()) s.add(l);
        for (const l of this.allowedCodePoints) s.delete(l);
        return s;
      }

      shouldHighlightNonBasicASCII(s, l) {
        const u = s.codePointAt(0);
        if (this.allowedCodePoints.has(u)) return 0;
        if (this.options.nonBasicASCII) return 1;
        let b = !1,
          f = !1;
        if (l) {
          for (const y of l) {
            const w = y.codePointAt(0),
              E = i.isBasicASCII(y);
            b = b || E, !E && !this.ambiguousCharacters.isAmbiguous(w) && !i.InvisibleCharacters.isInvisibleCharacter(w) && (f = !0);
          }
        }
        return !b && f ? 0 : this.options.invisibleCharacters && !L(s) && i.InvisibleCharacters.isInvisibleCharacter(u) ? 2 : this.options.ambiguousCharacters && this.ambiguousCharacters.isAmbiguous(u) ? 3 : 0;
      }
    }

    function L(e) {
      return e === ' ' || e === `
` || e === '	';
    }
  }), X(J[58], Z([0, 1]), function(q, t) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.WrappingIndent = t.TrackedRangeStickiness = t.TextEditorCursorStyle = t.TextEditorCursorBlinkingStyle = t.SymbolTag = t.SymbolKind = t.SignatureHelpTriggerKind = t.ShowLightbulbIconMode = t.SelectionDirection = t.ScrollbarVisibility = t.ScrollType = t.RenderMinimap = t.RenderLineNumbersType = t.PositionAffinity = t.OverviewRulerLane = t.OverlayWidgetPositionPreference = t.NewSymbolNameTag = t.MouseTargetType = t.MinimapPosition = t.MarkerTag = t.MarkerSeverity = t.KeyCode = t.InlineEditTriggerKind = t.InlineCompletionTriggerKind = t.InlayHintKind = t.InjectedTextCursorStops = t.IndentAction = t.GlyphMarginLane = t.EndOfLineSequence = t.EndOfLinePreference = t.EditorOption = t.EditorAutoIndentStrategy = t.DocumentHighlightKind = t.DefaultEndOfLine = t.CursorChangeReason = t.ContentWidgetPositionPreference = t.CompletionTriggerKind = t.CompletionItemTag = t.CompletionItemKind = t.CompletionItemInsertTextRule = t.CodeActionTriggerType = t.AccessibilitySupport = void 0;
    var M;
    (function(n) {
      n[n.Unknown = 0] = 'Unknown', n[n.Disabled = 1] = 'Disabled', n[n.Enabled = 2] = 'Enabled';
    })(M || (t.AccessibilitySupport = M = {}));
    var R;
    (function(n) {
      n[n.Invoke = 1] = 'Invoke', n[n.Auto = 2] = 'Auto';
    })(R || (t.CodeActionTriggerType = R = {}));
    var i;
    (function(n) {
      n[n.None = 0] = 'None', n[n.KeepWhitespace = 1] = 'KeepWhitespace', n[n.InsertAsSnippet = 4] = 'InsertAsSnippet';
    })(i || (t.CompletionItemInsertTextRule = i = {}));
    var d;
    (function(n) {
      n[n.Method = 0] = 'Method', n[n.Function = 1] = 'Function', n[n.Constructor = 2] = 'Constructor', n[n.Field = 3] = 'Field', n[n.Variable = 4] = 'Variable', n[n.Class = 5] = 'Class', n[n.Struct = 6] = 'Struct', n[n.Interface = 7] = 'Interface', n[n.Module = 8] = 'Module', n[n.Property = 9] = 'Property', n[n.Event = 10] = 'Event', n[n.Operator = 11] = 'Operator', n[n.Unit = 12] = 'Unit', n[n.Value = 13] = 'Value', n[n.Constant = 14] = 'Constant', n[n.Enum = 15] = 'Enum', n[n.EnumMember = 16] = 'EnumMember', n[n.Keyword = 17] = 'Keyword', n[n.Text = 18] = 'Text', n[n.Color = 19] = 'Color', n[n.File = 20] = 'File', n[n.Reference = 21] = 'Reference', n[n.Customcolor = 22] = 'Customcolor', n[n.Folder = 23] = 'Folder', n[n.TypeParameter = 24] = 'TypeParameter', n[n.User = 25] = 'User', n[n.Issue = 26] = 'Issue', n[n.Snippet = 27] = 'Snippet';
    })(d || (t.CompletionItemKind = d = {}));
    var _;
    (function(n) {
      n[n.Deprecated = 1] = 'Deprecated';
    })(_ || (t.CompletionItemTag = _ = {}));
    var p;
    (function(n) {
      n[n.Invoke = 0] = 'Invoke', n[n.TriggerCharacter = 1] = 'TriggerCharacter', n[n.TriggerForIncompleteCompletions = 2] = 'TriggerForIncompleteCompletions';
    })(p || (t.CompletionTriggerKind = p = {}));
    var c;
    (function(n) {
      n[n.EXACT = 0] = 'EXACT', n[n.ABOVE = 1] = 'ABOVE', n[n.BELOW = 2] = 'BELOW';
    })(c || (t.ContentWidgetPositionPreference = c = {}));
    var o;
    (function(n) {
      n[n.NotSet = 0] = 'NotSet', n[n.ContentFlush = 1] = 'ContentFlush', n[n.RecoverFromMarkers = 2] = 'RecoverFromMarkers', n[n.Explicit = 3] = 'Explicit', n[n.Paste = 4] = 'Paste', n[n.Undo = 5] = 'Undo', n[n.Redo = 6] = 'Redo';
    })(o || (t.CursorChangeReason = o = {}));
    var L;
    (function(n) {
      n[n.LF = 1] = 'LF', n[n.CRLF = 2] = 'CRLF';
    })(L || (t.DefaultEndOfLine = L = {}));
    var e;
    (function(n) {
      n[n.Text = 0] = 'Text', n[n.Read = 1] = 'Read', n[n.Write = 2] = 'Write';
    })(e || (t.DocumentHighlightKind = e = {}));
    var s;
    (function(n) {
      n[n.None = 0] = 'None', n[n.Keep = 1] = 'Keep', n[n.Brackets = 2] = 'Brackets', n[n.Advanced = 3] = 'Advanced', n[n.Full = 4] = 'Full';
    })(s || (t.EditorAutoIndentStrategy = s = {}));
    var l;
    (function(n) {
      n[n.acceptSuggestionOnCommitCharacter = 0] = 'acceptSuggestionOnCommitCharacter', n[n.acceptSuggestionOnEnter = 1] = 'acceptSuggestionOnEnter', n[n.accessibilitySupport = 2] = 'accessibilitySupport', n[n.accessibilityPageSize = 3] = 'accessibilityPageSize', n[n.ariaLabel = 4] = 'ariaLabel', n[n.ariaRequired = 5] = 'ariaRequired', n[n.autoClosingBrackets = 6] = 'autoClosingBrackets', n[n.autoClosingComments = 7] = 'autoClosingComments', n[n.screenReaderAnnounceInlineSuggestion = 8] = 'screenReaderAnnounceInlineSuggestion', n[n.autoClosingDelete = 9] = 'autoClosingDelete', n[n.autoClosingOvertype = 10] = 'autoClosingOvertype', n[n.autoClosingQuotes = 11] = 'autoClosingQuotes', n[n.autoIndent = 12] = 'autoIndent', n[n.automaticLayout = 13] = 'automaticLayout', n[n.autoSurround = 14] = 'autoSurround', n[n.bracketPairColorization = 15] = 'bracketPairColorization', n[n.guides = 16] = 'guides', n[n.codeLens = 17] = 'codeLens', n[n.codeLensFontFamily = 18] = 'codeLensFontFamily', n[n.codeLensFontSize = 19] = 'codeLensFontSize', n[n.colorDecorators = 20] = 'colorDecorators', n[n.colorDecoratorsLimit = 21] = 'colorDecoratorsLimit', n[n.columnSelection = 22] = 'columnSelection', n[n.comments = 23] = 'comments', n[n.contextmenu = 24] = 'contextmenu', n[n.copyWithSyntaxHighlighting = 25] = 'copyWithSyntaxHighlighting', n[n.cursorBlinking = 26] = 'cursorBlinking', n[n.cursorSmoothCaretAnimation = 27] = 'cursorSmoothCaretAnimation', n[n.cursorStyle = 28] = 'cursorStyle', n[n.cursorSurroundingLines = 29] = 'cursorSurroundingLines', n[n.cursorSurroundingLinesStyle = 30] = 'cursorSurroundingLinesStyle', n[n.cursorWidth = 31] = 'cursorWidth', n[n.disableLayerHinting = 32] = 'disableLayerHinting', n[n.disableMonospaceOptimizations = 33] = 'disableMonospaceOptimizations', n[n.domReadOnly = 34] = 'domReadOnly', n[n.dragAndDrop = 35] = 'dragAndDrop', n[n.dropIntoEditor = 36] = 'dropIntoEditor', n[n.emptySelectionClipboard = 37] = 'emptySelectionClipboard', n[n.experimentalWhitespaceRendering = 38] = 'experimentalWhitespaceRendering', n[n.extraEditorClassName = 39] = 'extraEditorClassName', n[n.fastScrollSensitivity = 40] = 'fastScrollSensitivity', n[n.find = 41] = 'find', n[n.fixedOverflowWidgets = 42] = 'fixedOverflowWidgets', n[n.folding = 43] = 'folding', n[n.foldingStrategy = 44] = 'foldingStrategy', n[n.foldingHighlight = 45] = 'foldingHighlight', n[n.foldingImportsByDefault = 46] = 'foldingImportsByDefault', n[n.foldingMaximumRegions = 47] = 'foldingMaximumRegions', n[n.unfoldOnClickAfterEndOfLine = 48] = 'unfoldOnClickAfterEndOfLine', n[n.fontFamily = 49] = 'fontFamily', n[n.fontInfo = 50] = 'fontInfo', n[n.fontLigatures = 51] = 'fontLigatures', n[n.fontSize = 52] = 'fontSize', n[n.fontWeight = 53] = 'fontWeight', n[n.fontVariations = 54] = 'fontVariations', n[n.formatOnPaste = 55] = 'formatOnPaste', n[n.formatOnType = 56] = 'formatOnType', n[n.glyphMargin = 57] = 'glyphMargin', n[n.gotoLocation = 58] = 'gotoLocation', n[n.hideCursorInOverviewRuler = 59] = 'hideCursorInOverviewRuler', n[n.hover = 60] = 'hover', n[n.inDiffEditor = 61] = 'inDiffEditor', n[n.inlineSuggest = 62] = 'inlineSuggest', n[n.inlineEdit = 63] = 'inlineEdit', n[n.letterSpacing = 64] = 'letterSpacing', n[n.lightbulb = 65] = 'lightbulb', n[n.lineDecorationsWidth = 66] = 'lineDecorationsWidth', n[n.lineHeight = 67] = 'lineHeight', n[n.lineNumbers = 68] = 'lineNumbers', n[n.lineNumbersMinChars = 69] = 'lineNumbersMinChars', n[n.linkedEditing = 70] = 'linkedEditing', n[n.links = 71] = 'links', n[n.matchBrackets = 72] = 'matchBrackets', n[n.minimap = 73] = 'minimap', n[n.mouseStyle = 74] = 'mouseStyle', n[n.mouseWheelScrollSensitivity = 75] = 'mouseWheelScrollSensitivity', n[n.mouseWheelZoom = 76] = 'mouseWheelZoom', n[n.multiCursorMergeOverlapping = 77] = 'multiCursorMergeOverlapping', n[n.multiCursorModifier = 78] = 'multiCursorModifier', n[n.multiCursorPaste = 79] = 'multiCursorPaste', n[n.multiCursorLimit = 80] = 'multiCursorLimit', n[n.occurrencesHighlight = 81] = 'occurrencesHighlight', n[n.overviewRulerBorder = 82] = 'overviewRulerBorder', n[n.overviewRulerLanes = 83] = 'overviewRulerLanes', n[n.padding = 84] = 'padding', n[n.pasteAs = 85] = 'pasteAs', n[n.parameterHints = 86] = 'parameterHints', n[n.peekWidgetDefaultFocus = 87] = 'peekWidgetDefaultFocus', n[n.definitionLinkOpensInPeek = 88] = 'definitionLinkOpensInPeek', n[n.quickSuggestions = 89] = 'quickSuggestions', n[n.quickSuggestionsDelay = 90] = 'quickSuggestionsDelay', n[n.readOnly = 91] = 'readOnly', n[n.readOnlyMessage = 92] = 'readOnlyMessage', n[n.renameOnType = 93] = 'renameOnType', n[n.renderControlCharacters = 94] = 'renderControlCharacters', n[n.renderFinalNewline = 95] = 'renderFinalNewline', n[n.renderLineHighlight = 96] = 'renderLineHighlight', n[n.renderLineHighlightOnlyWhenFocus = 97] = 'renderLineHighlightOnlyWhenFocus', n[n.renderValidationDecorations = 98] = 'renderValidationDecorations', n[n.renderWhitespace = 99] = 'renderWhitespace', n[n.revealHorizontalRightPadding = 100] = 'revealHorizontalRightPadding',n[n.roundedSelection = 101] = 'roundedSelection',n[n.rulers = 102] = 'rulers',n[n.scrollbar = 103] = 'scrollbar',n[n.scrollBeyondLastColumn = 104] = 'scrollBeyondLastColumn',n[n.scrollBeyondLastLine = 105] = 'scrollBeyondLastLine',n[n.scrollPredominantAxis = 106] = 'scrollPredominantAxis',n[n.selectionClipboard = 107] = 'selectionClipboard',n[n.selectionHighlight = 108] = 'selectionHighlight',n[n.selectOnLineNumbers = 109] = 'selectOnLineNumbers',n[n.showFoldingControls = 110] = 'showFoldingControls',n[n.showUnused = 111] = 'showUnused',n[n.snippetSuggestions = 112] = 'snippetSuggestions',n[n.smartSelect = 113] = 'smartSelect',n[n.smoothScrolling = 114] = 'smoothScrolling',n[n.stickyScroll = 115] = 'stickyScroll',n[n.stickyTabStops = 116] = 'stickyTabStops',n[n.stopRenderingLineAfter = 117] = 'stopRenderingLineAfter',n[n.suggest = 118] = 'suggest',n[n.suggestFontSize = 119] = 'suggestFontSize',n[n.suggestLineHeight = 120] = 'suggestLineHeight',n[n.suggestOnTriggerCharacters = 121] = 'suggestOnTriggerCharacters',n[n.suggestSelection = 122] = 'suggestSelection',n[n.tabCompletion = 123] = 'tabCompletion',n[n.tabIndex = 124] = 'tabIndex',n[n.unicodeHighlighting = 125] = 'unicodeHighlighting',n[n.unusualLineTerminators = 126] = 'unusualLineTerminators',n[n.useShadowDOM = 127] = 'useShadowDOM',n[n.useTabStops = 128] = 'useTabStops',n[n.wordBreak = 129] = 'wordBreak',n[n.wordSeparators = 130] = 'wordSeparators',n[n.wordWrap = 131] = 'wordWrap',n[n.wordWrapBreakAfterCharacters = 132] = 'wordWrapBreakAfterCharacters',n[n.wordWrapBreakBeforeCharacters = 133] = 'wordWrapBreakBeforeCharacters',n[n.wordWrapColumn = 134] = 'wordWrapColumn',n[n.wordWrapOverride1 = 135] = 'wordWrapOverride1',n[n.wordWrapOverride2 = 136] = 'wordWrapOverride2',n[n.wrappingIndent = 137] = 'wrappingIndent',n[n.wrappingStrategy = 138] = 'wrappingStrategy',n[n.showDeprecated = 139] = 'showDeprecated',n[n.inlayHints = 140] = 'inlayHints',n[n.editorClassName = 141] = 'editorClassName',n[n.pixelRatio = 142] = 'pixelRatio',n[n.tabFocusMode = 143] = 'tabFocusMode',n[n.layoutInfo = 144] = 'layoutInfo',n[n.wrappingInfo = 145] = 'wrappingInfo',n[n.defaultColorDecorators = 146] = 'defaultColorDecorators',n[n.colorDecoratorsActivatedOn = 147] = 'colorDecoratorsActivatedOn',n[n.inlineCompletionsAccessibilityVerbose = 148] = 'inlineCompletionsAccessibilityVerbose';
    })(l || (t.EditorOption = l = {}));
    var u;
    (function(n) {
      n[n.TextDefined = 0] = 'TextDefined', n[n.LF = 1] = 'LF', n[n.CRLF = 2] = 'CRLF';
    })(u || (t.EndOfLinePreference = u = {}));
    var b;
    (function(n) {
      n[n.LF = 0] = 'LF', n[n.CRLF = 1] = 'CRLF';
    })(b || (t.EndOfLineSequence = b = {}));
    var f;
    (function(n) {
      n[n.Left = 1] = 'Left', n[n.Center = 2] = 'Center', n[n.Right = 3] = 'Right';
    })(f || (t.GlyphMarginLane = f = {}));
    var y;
    (function(n) {
      n[n.None = 0] = 'None', n[n.Indent = 1] = 'Indent', n[n.IndentOutdent = 2] = 'IndentOutdent', n[n.Outdent = 3] = 'Outdent';
    })(y || (t.IndentAction = y = {}));
    var w;
    (function(n) {
      n[n.Both = 0] = 'Both', n[n.Right = 1] = 'Right', n[n.Left = 2] = 'Left', n[n.None = 3] = 'None';
    })(w || (t.InjectedTextCursorStops = w = {}));
    var E;
    (function(n) {
      n[n.Type = 1] = 'Type', n[n.Parameter = 2] = 'Parameter';
    })(E || (t.InlayHintKind = E = {}));
    var S;
    (function(n) {
      n[n.Automatic = 0] = 'Automatic', n[n.Explicit = 1] = 'Explicit';
    })(S || (t.InlineCompletionTriggerKind = S = {}));
    var C;
    (function(n) {
      n[n.Invoke = 0] = 'Invoke', n[n.Automatic = 1] = 'Automatic';
    })(C || (t.InlineEditTriggerKind = C = {}));
    var r;
    (function(n) {
      n[n.DependsOnKbLayout = -1] = 'DependsOnKbLayout', n[n.Unknown = 0] = 'Unknown', n[n.Backspace = 1] = 'Backspace', n[n.Tab = 2] = 'Tab', n[n.Enter = 3] = 'Enter', n[n.Shift = 4] = 'Shift', n[n.Ctrl = 5] = 'Ctrl', n[n.Alt = 6] = 'Alt', n[n.PauseBreak = 7] = 'PauseBreak', n[n.CapsLock = 8] = 'CapsLock', n[n.Escape = 9] = 'Escape', n[n.Space = 10] = 'Space', n[n.PageUp = 11] = 'PageUp', n[n.PageDown = 12] = 'PageDown', n[n.End = 13] = 'End', n[n.Home = 14] = 'Home', n[n.LeftArrow = 15] = 'LeftArrow', n[n.UpArrow = 16] = 'UpArrow', n[n.RightArrow = 17] = 'RightArrow', n[n.DownArrow = 18] = 'DownArrow', n[n.Insert = 19] = 'Insert', n[n.Delete = 20] = 'Delete', n[n.Digit0 = 21] = 'Digit0', n[n.Digit1 = 22] = 'Digit1', n[n.Digit2 = 23] = 'Digit2', n[n.Digit3 = 24] = 'Digit3', n[n.Digit4 = 25] = 'Digit4', n[n.Digit5 = 26] = 'Digit5', n[n.Digit6 = 27] = 'Digit6', n[n.Digit7 = 28] = 'Digit7', n[n.Digit8 = 29] = 'Digit8', n[n.Digit9 = 30] = 'Digit9', n[n.KeyA = 31] = 'KeyA', n[n.KeyB = 32] = 'KeyB', n[n.KeyC = 33] = 'KeyC', n[n.KeyD = 34] = 'KeyD', n[n.KeyE = 35] = 'KeyE', n[n.KeyF = 36] = 'KeyF', n[n.KeyG = 37] = 'KeyG', n[n.KeyH = 38] = 'KeyH', n[n.KeyI = 39] = 'KeyI', n[n.KeyJ = 40] = 'KeyJ', n[n.KeyK = 41] = 'KeyK', n[n.KeyL = 42] = 'KeyL', n[n.KeyM = 43] = 'KeyM', n[n.KeyN = 44] = 'KeyN', n[n.KeyO = 45] = 'KeyO', n[n.KeyP = 46] = 'KeyP', n[n.KeyQ = 47] = 'KeyQ', n[n.KeyR = 48] = 'KeyR', n[n.KeyS = 49] = 'KeyS', n[n.KeyT = 50] = 'KeyT', n[n.KeyU = 51] = 'KeyU', n[n.KeyV = 52] = 'KeyV', n[n.KeyW = 53] = 'KeyW', n[n.KeyX = 54] = 'KeyX', n[n.KeyY = 55] = 'KeyY', n[n.KeyZ = 56] = 'KeyZ', n[n.Meta = 57] = 'Meta', n[n.ContextMenu = 58] = 'ContextMenu', n[n.F1 = 59] = 'F1', n[n.F2 = 60] = 'F2', n[n.F3 = 61] = 'F3', n[n.F4 = 62] = 'F4', n[n.F5 = 63] = 'F5', n[n.F6 = 64] = 'F6', n[n.F7 = 65] = 'F7', n[n.F8 = 66] = 'F8', n[n.F9 = 67] = 'F9', n[n.F10 = 68] = 'F10', n[n.F11 = 69] = 'F11', n[n.F12 = 70] = 'F12', n[n.F13 = 71] = 'F13', n[n.F14 = 72] = 'F14', n[n.F15 = 73] = 'F15', n[n.F16 = 74] = 'F16', n[n.F17 = 75] = 'F17', n[n.F18 = 76] = 'F18', n[n.F19 = 77] = 'F19', n[n.F20 = 78] = 'F20', n[n.F21 = 79] = 'F21', n[n.F22 = 80] = 'F22', n[n.F23 = 81] = 'F23', n[n.F24 = 82] = 'F24', n[n.NumLock = 83] = 'NumLock', n[n.ScrollLock = 84] = 'ScrollLock', n[n.Semicolon = 85] = 'Semicolon', n[n.Equal = 86] = 'Equal', n[n.Comma = 87] = 'Comma', n[n.Minus = 88] = 'Minus', n[n.Period = 89] = 'Period', n[n.Slash = 90] = 'Slash', n[n.Backquote = 91] = 'Backquote', n[n.BracketLeft = 92] = 'BracketLeft', n[n.Backslash = 93] = 'Backslash', n[n.BracketRight = 94] = 'BracketRight', n[n.Quote = 95] = 'Quote', n[n.OEM_8 = 96] = 'OEM_8', n[n.IntlBackslash = 97] = 'IntlBackslash', n[n.Numpad0 = 98] = 'Numpad0', n[n.Numpad1 = 99] = 'Numpad1',n[n.Numpad2 = 100] = 'Numpad2',n[n.Numpad3 = 101] = 'Numpad3',n[n.Numpad4 = 102] = 'Numpad4',n[n.Numpad5 = 103] = 'Numpad5',n[n.Numpad6 = 104] = 'Numpad6',n[n.Numpad7 = 105] = 'Numpad7',n[n.Numpad8 = 106] = 'Numpad8',n[n.Numpad9 = 107] = 'Numpad9',n[n.NumpadMultiply = 108] = 'NumpadMultiply',n[n.NumpadAdd = 109] = 'NumpadAdd',n[n.NUMPAD_SEPARATOR = 110] = 'NUMPAD_SEPARATOR',n[n.NumpadSubtract = 111] = 'NumpadSubtract',n[n.NumpadDecimal = 112] = 'NumpadDecimal',n[n.NumpadDivide = 113] = 'NumpadDivide',n[n.KEY_IN_COMPOSITION = 114] = 'KEY_IN_COMPOSITION',n[n.ABNT_C1 = 115] = 'ABNT_C1',n[n.ABNT_C2 = 116] = 'ABNT_C2',n[n.AudioVolumeMute = 117] = 'AudioVolumeMute',n[n.AudioVolumeUp = 118] = 'AudioVolumeUp',n[n.AudioVolumeDown = 119] = 'AudioVolumeDown',n[n.BrowserSearch = 120] = 'BrowserSearch',n[n.BrowserHome = 121] = 'BrowserHome',n[n.BrowserBack = 122] = 'BrowserBack',n[n.BrowserForward = 123] = 'BrowserForward',n[n.MediaTrackNext = 124] = 'MediaTrackNext',n[n.MediaTrackPrevious = 125] = 'MediaTrackPrevious',n[n.MediaStop = 126] = 'MediaStop',n[n.MediaPlayPause = 127] = 'MediaPlayPause',n[n.LaunchMediaPlayer = 128] = 'LaunchMediaPlayer',n[n.LaunchMail = 129] = 'LaunchMail',n[n.LaunchApp2 = 130] = 'LaunchApp2',n[n.Clear = 131] = 'Clear',n[n.MAX_VALUE = 132] = 'MAX_VALUE';
    })(r || (t.KeyCode = r = {}));
    var a;
    (function(n) {
      n[n.Hint = 1] = 'Hint', n[n.Info = 2] = 'Info', n[n.Warning = 4] = 'Warning', n[n.Error = 8] = 'Error';
    })(a || (t.MarkerSeverity = a = {}));
    var g;
    (function(n) {
      n[n.Unnecessary = 1] = 'Unnecessary', n[n.Deprecated = 2] = 'Deprecated';
    })(g || (t.MarkerTag = g = {}));
    var m;
    (function(n) {
      n[n.Inline = 1] = 'Inline', n[n.Gutter = 2] = 'Gutter';
    })(m || (t.MinimapPosition = m = {}));
    var h;
    (function(n) {
      n[n.UNKNOWN = 0] = 'UNKNOWN', n[n.TEXTAREA = 1] = 'TEXTAREA', n[n.GUTTER_GLYPH_MARGIN = 2] = 'GUTTER_GLYPH_MARGIN', n[n.GUTTER_LINE_NUMBERS = 3] = 'GUTTER_LINE_NUMBERS', n[n.GUTTER_LINE_DECORATIONS = 4] = 'GUTTER_LINE_DECORATIONS', n[n.GUTTER_VIEW_ZONE = 5] = 'GUTTER_VIEW_ZONE', n[n.CONTENT_TEXT = 6] = 'CONTENT_TEXT', n[n.CONTENT_EMPTY = 7] = 'CONTENT_EMPTY', n[n.CONTENT_VIEW_ZONE = 8] = 'CONTENT_VIEW_ZONE', n[n.CONTENT_WIDGET = 9] = 'CONTENT_WIDGET', n[n.OVERVIEW_RULER = 10] = 'OVERVIEW_RULER', n[n.SCROLLBAR = 11] = 'SCROLLBAR', n[n.OVERLAY_WIDGET = 12] = 'OVERLAY_WIDGET', n[n.OUTSIDE_EDITOR = 13] = 'OUTSIDE_EDITOR';
    })(h || (t.MouseTargetType = h = {}));
    var v;
    (function(n) {
      n[n.AIGenerated = 1] = 'AIGenerated';
    })(v || (t.NewSymbolNameTag = v = {}));
    var N;
    (function(n) {
      n[n.TOP_RIGHT_CORNER = 0] = 'TOP_RIGHT_CORNER', n[n.BOTTOM_RIGHT_CORNER = 1] = 'BOTTOM_RIGHT_CORNER', n[n.TOP_CENTER = 2] = 'TOP_CENTER';
    })(N || (t.OverlayWidgetPositionPreference = N = {}));
    var A;
    (function(n) {
      n[n.Left = 1] = 'Left', n[n.Center = 2] = 'Center', n[n.Right = 4] = 'Right', n[n.Full = 7] = 'Full';
    })(A || (t.OverviewRulerLane = A = {}));
    var D;
    (function(n) {
      n[n.Left = 0] = 'Left', n[n.Right = 1] = 'Right', n[n.None = 2] = 'None', n[n.LeftOfInjectedText = 3] = 'LeftOfInjectedText', n[n.RightOfInjectedText = 4] = 'RightOfInjectedText';
    })(D || (t.PositionAffinity = D = {}));
    var P;
    (function(n) {
      n[n.Off = 0] = 'Off', n[n.On = 1] = 'On', n[n.Relative = 2] = 'Relative', n[n.Interval = 3] = 'Interval', n[n.Custom = 4] = 'Custom';
    })(P || (t.RenderLineNumbersType = P = {}));
    var T;
    (function(n) {
      n[n.None = 0] = 'None', n[n.Text = 1] = 'Text', n[n.Blocks = 2] = 'Blocks';
    })(T || (t.RenderMinimap = T = {}));
    var I;
    (function(n) {
      n[n.Smooth = 0] = 'Smooth', n[n.Immediate = 1] = 'Immediate';
    })(I || (t.ScrollType = I = {}));
    var B;
    (function(n) {
      n[n.Auto = 1] = 'Auto', n[n.Hidden = 2] = 'Hidden', n[n.Visible = 3] = 'Visible';
    })(B || (t.ScrollbarVisibility = B = {}));
    var z;
    (function(n) {
      n[n.LTR = 0] = 'LTR', n[n.RTL = 1] = 'RTL';
    })(z || (t.SelectionDirection = z = {}));
    var x;
    (function(n) {
      n.Off = 'off', n.OnCode = 'onCode', n.On = 'on';
    })(x || (t.ShowLightbulbIconMode = x = {}));
    var O;
    (function(n) {
      n[n.Invoke = 1] = 'Invoke', n[n.TriggerCharacter = 2] = 'TriggerCharacter', n[n.ContentChange = 3] = 'ContentChange';
    })(O || (t.SignatureHelpTriggerKind = O = {}));
    var F;
    (function(n) {
      n[n.File = 0] = 'File', n[n.Module = 1] = 'Module', n[n.Namespace = 2] = 'Namespace', n[n.Package = 3] = 'Package', n[n.Class = 4] = 'Class', n[n.Method = 5] = 'Method', n[n.Property = 6] = 'Property', n[n.Field = 7] = 'Field', n[n.Constructor = 8] = 'Constructor', n[n.Enum = 9] = 'Enum', n[n.Interface = 10] = 'Interface', n[n.Function = 11] = 'Function', n[n.Variable = 12] = 'Variable', n[n.Constant = 13] = 'Constant', n[n.String = 14] = 'String', n[n.Number = 15] = 'Number', n[n.Boolean = 16] = 'Boolean', n[n.Array = 17] = 'Array', n[n.Object = 18] = 'Object', n[n.Key = 19] = 'Key', n[n.Null = 20] = 'Null', n[n.EnumMember = 21] = 'EnumMember', n[n.Struct = 22] = 'Struct', n[n.Event = 23] = 'Event', n[n.Operator = 24] = 'Operator', n[n.TypeParameter = 25] = 'TypeParameter';
    })(F || (t.SymbolKind = F = {}));
    var W;
    (function(n) {
      n[n.Deprecated = 1] = 'Deprecated';
    })(W || (t.SymbolTag = W = {}));
    var H;
    (function(n) {
      n[n.Hidden = 0] = 'Hidden', n[n.Blink = 1] = 'Blink', n[n.Smooth = 2] = 'Smooth', n[n.Phase = 3] = 'Phase', n[n.Expand = 4] = 'Expand', n[n.Solid = 5] = 'Solid';
    })(H || (t.TextEditorCursorBlinkingStyle = H = {}));
    var G;
    (function(n) {
      n[n.Line = 1] = 'Line', n[n.Block = 2] = 'Block', n[n.Underline = 3] = 'Underline', n[n.LineThin = 4] = 'LineThin', n[n.BlockOutline = 5] = 'BlockOutline', n[n.UnderlineThin = 6] = 'UnderlineThin';
    })(G || (t.TextEditorCursorStyle = G = {}));
    var ne;
    (function(n) {
      n[n.AlwaysGrowsWhenTypingAtEdges = 0] = 'AlwaysGrowsWhenTypingAtEdges', n[n.NeverGrowsWhenTypingAtEdges = 1] = 'NeverGrowsWhenTypingAtEdges', n[n.GrowsOnlyWhenTypingBefore = 2] = 'GrowsOnlyWhenTypingBefore', n[n.GrowsOnlyWhenTypingAfter = 3] = 'GrowsOnlyWhenTypingAfter';
    })(ne || (t.TrackedRangeStickiness = ne = {}));
    var se;
    (function(n) {
      n[n.None = 0] = 'None', n[n.Same = 1] = 'Same', n[n.Indent = 2] = 'Indent', n[n.DeepIndent = 3] = 'DeepIndent';
    })(se || (t.WrappingIndent = se = {}));
  }), X(J[59], Z([0, 1, 9, 13]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.TokenizationRegistry = void 0;

    class i {
      constructor() {
        this._tokenizationSupports = new Map, this._factories = new Map, this._onDidChange = new M.Emitter, this.onDidChange = this._onDidChange.event, this._colorMap = null;
      }

      handleChange(p) {
        this._onDidChange.fire({
          changedLanguages: p,
          changedColorMap: !1,
        });
      }

      register(p, c) {
        return this._tokenizationSupports.set(p, c), this.handleChange([p]), (0, R.toDisposable)(() => {
          this._tokenizationSupports.get(p) === c && (this._tokenizationSupports.delete(p), this.handleChange([p]));
        });
      }

      get(p) {
        return this._tokenizationSupports.get(p) || null;
      }

      registerFactory(p, c) {
        var o;
        (o = this._factories.get(p)) === null || o === void 0 || o.dispose();
        const L = new d(this, p, c);
        return this._factories.set(p, L), (0, R.toDisposable)(() => {
          const e = this._factories.get(p);
          !e || e !== L || (this._factories.delete(p), e.dispose());
        });
      }

      async getOrCreate(p) {
        const c = this.get(p);
        if (c) return c;
        const o = this._factories.get(p);
        return !o || o.isResolved ? null : (await o.resolve(), this.get(p));
      }

      isResolved(p) {
        if (this.get(p)) return !0;
        const o = this._factories.get(p);
        return !!(!o || o.isResolved);
      }

      setColorMap(p) {
        this._colorMap = p, this._onDidChange.fire({
          changedLanguages: Array.from(this._tokenizationSupports.keys()),
          changedColorMap: !0,
        });
      }

      getColorMap() {
        return this._colorMap;
      }

      getDefaultBackground() {
        return this._colorMap && this._colorMap.length > 2 ? this._colorMap[2] : null;
      }
    }

    t.TokenizationRegistry = i;

    class d extends R.Disposable {
      constructor(p, c, o) {
        super(), this._registry = p, this._languageId = c, this._factory = o, this._isDisposed = !1, this._resolvePromise = null, this._isResolved = !1;
      }

      get isResolved() {
        return this._isResolved;
      }

      dispose() {
        this._isDisposed = !0, super.dispose();
      }

      async resolve() {
        return this._resolvePromise || (this._resolvePromise = this._create()), this._resolvePromise;
      }

      async _create() {
        const p = await this._factory.tokenizationSupport;
        this._isResolved = !0, p && !this._isDisposed && this._register(this._registry.register(this._languageId, p));
      }
    }
  }), X(J[60], Z([19, 61]), function(q, t) {
    return q.create('vs/base/common/platform', t);
  }), X(J[17], Z([0, 1, 60]), function(q, t, M) {
    'use strict';
    var R;
    Object.defineProperty(t, '__esModule', { value: !0 }), t.isAndroid = t.isEdge = t.isSafari = t.isFirefox = t.isChrome = t.isLittleEndian = t.OS = t.setTimeout0 = t.setTimeout0IsFaster = t.language = t.userAgent = t.isMobile = t.isIOS = t.webWorkerOrigin = t.isWebWorker = t.isWeb = t.isNative = t.isLinux = t.isMacintosh = t.isWindows = t.LANGUAGE_DEFAULT = void 0, t.LANGUAGE_DEFAULT = 'en';
    let i = !1,
      d = !1,
      _ = !1,
      p = !1,
      c = !1,
      o = !1,
      L = !1,
      e = !1,
      s = !1,
      l = !1,
      u,
      b = t.LANGUAGE_DEFAULT,
      f = t.LANGUAGE_DEFAULT,
      y,
      w;
    const E = globalThis;
    let S;
    typeof E.vscode < 'u' && typeof E.vscode.process < 'u' ? S = E.vscode.process : typeof process < 'u' && (S = process);
    const C = typeof ((R = S?.versions) === null || R === void 0 ? void 0 : R.electron) == 'string',
      r = C && S?.type === 'renderer';
    if (typeof S == 'object') {
      i = S.platform === 'win32', d = S.platform === 'darwin', _ = S.platform === 'linux', p = _ && !!S.env.SNAP && !!S.env.SNAP_REVISION, L = C, s = !!S.env.CI || !!S.env.BUILD_ARTIFACTSTAGINGDIRECTORY, u = t.LANGUAGE_DEFAULT, b = t.LANGUAGE_DEFAULT;
      const v = S.env.VSCODE_NLS_CONFIG;
      if (v) {
        try {
          const N = JSON.parse(v),
            A = N.availableLanguages['*'];
          u = N.locale, f = N.osLocale, b = A || t.LANGUAGE_DEFAULT, y = N._translationsConfigFile;
        } catch {
        }
      }
      c = !0;
    } else {
      typeof navigator == 'object' && !r ? (w = navigator.userAgent, i = w.indexOf('Windows') >= 0, d = w.indexOf('Macintosh') >= 0, e = (w.indexOf('Macintosh') >= 0 || w.indexOf('iPad') >= 0 || w.indexOf('iPhone') >= 0) && !!navigator.maxTouchPoints && navigator.maxTouchPoints > 0, _ = w.indexOf('Linux') >= 0, l = w?.indexOf('Mobi') >= 0, o = !0, u = M.getConfiguredDefaultLocale(M.localize(0, null)) || t.LANGUAGE_DEFAULT, b = u, f = navigator.language) : console.error('Unable to resolve platform.');
    }
    let a = 0;
    d ? a = 1 : i ? a = 3 : _ && (a = 2), t.isWindows = i, t.isMacintosh = d, t.isLinux = _, t.isNative = c, t.isWeb = o, t.isWebWorker = o && typeof E.importScripts == 'function', t.webWorkerOrigin = t.isWebWorker ? E.origin : void 0, t.isIOS = e, t.isMobile = l, t.userAgent = w, t.language = b, t.setTimeout0IsFaster = typeof E.postMessage == 'function' && !E.importScripts, t.setTimeout0 = (() => {
      if (t.setTimeout0IsFaster) {
        const v = [];
        E.addEventListener('message', A => {
          if (A.data && A.data.vscodeScheduleAsyncWork) {
            for (let D = 0, P = v.length; D < P; D++) {
              const T = v[D];
              if (T.id === A.data.vscodeScheduleAsyncWork) {
                v.splice(D, 1), T.callback();
                return;
              }
            }
          }
        });
        let N = 0;
        return A => {
          const D = ++N;
          v.push({
            id: D,
            callback: A,
          }), E.postMessage({ vscodeScheduleAsyncWork: D }, '*');
        };
      }
      return v => setTimeout(v);
    })(), t.OS = d || e ? 2 : i ? 1 : 3;
    let g = !0,
      m = !1;

    function h() {
      if (!m) {
        m = !0;
        const v = new Uint8Array(2);
        v[0] = 1, v[1] = 2, g = new Uint16Array(v.buffer)[0] === 513;
      }
      return g;
    }

    t.isLittleEndian = h, t.isChrome = !!(t.userAgent && t.userAgent.indexOf('Chrome') >= 0), t.isFirefox = !!(t.userAgent && t.userAgent.indexOf('Firefox') >= 0), t.isSafari = !!(!t.isChrome && t.userAgent && t.userAgent.indexOf('Safari') >= 0), t.isEdge = !!(t.userAgent && t.userAgent.indexOf('Edg/') >= 0), t.isAndroid = !!(t.userAgent && t.userAgent.indexOf('Android') >= 0);
  }), X(J[62], Z([0, 1, 17]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.platform = t.env = t.cwd = void 0;
    let R;
    const i = globalThis.vscode;
    if (typeof i < 'u' && typeof i.process < 'u') {
      const d = i.process;
      R = {
        get platform() {
          return d.platform;
        },
        get arch() {
          return d.arch;
        },
        get env() {
          return d.env;
        },
        cwd() {
          return d.cwd();
        },
      };
    } else {
      typeof process < 'u' ? R = {
        get platform() {
          return process.platform;
        },
        get arch() {
          return process.arch;
        },
        get env() {
          return process.env;
        },
        cwd() {
          return process.env.VSCODE_CWD || process.cwd();
        },
      } : R = {
        get platform() {
          return M.isWindows ? 'win32' : M.isMacintosh ? 'darwin' : 'linux';
        },
        get arch() {
        },
        get env() {
          return {};
        },
        cwd() {
          return '/';
        },
      };
    }
    t.cwd = R.cwd, t.env = R.env, t.platform = R.platform;
  }), X(J[63], Z([0, 1, 62]), function(q, t, M) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.sep = t.extname = t.basename = t.dirname = t.relative = t.resolve = t.normalize = t.posix = t.win32 = void 0;
    const R = 65,
      i = 97,
      d = 90,
      _ = 122,
      p = 46,
      c = 47,
      o = 92,
      L = 58,
      e = 63;

    class s extends Error {
      constructor(a, g, m) {
        let h;
        typeof g == 'string' && g.indexOf('not ') === 0 ? (h = 'must not be', g = g.replace(/^not /, '')) : h = 'must be';
        const v = a.indexOf('.') !== -1 ? 'property' : 'argument';
        let N = `The "${a}" ${v} ${h} of type ${g}`;
        N += `. Received type ${typeof m}`, super(N), this.code = 'ERR_INVALID_ARG_TYPE';
      }
    }

    function l(r, a) {
      if (r === null || typeof r != 'object') throw new s(a, 'Object', r);
    }

    function u(r, a) {
      if (typeof r != 'string') throw new s(a, 'string', r);
    }

    const b = M.platform === 'win32';

    function f(r) {
      return r === c || r === o;
    }

    function y(r) {
      return r === c;
    }

    function w(r) {
      return r >= R && r <= d || r >= i && r <= _;
    }

    function E(r, a, g, m) {
      let h = '',
        v = 0,
        N = -1,
        A = 0,
        D = 0;
      for (let P = 0; P <= r.length; ++P) {
        if (P < r.length) {
          D = r.charCodeAt(P);
        } else {
          if (m(D)) break;
          D = c;
        }
        if (m(D)) {
          if (!(N === P - 1 || A === 1)) {
            if (A === 2) {
              if (h.length < 2 || v !== 2 || h.charCodeAt(h.length - 1) !== p || h.charCodeAt(h.length - 2) !== p) {
                if (h.length > 2) {
                  const T = h.lastIndexOf(g);
                  T === -1 ? (h = '', v = 0) : (h = h.slice(0, T), v = h.length - 1 - h.lastIndexOf(g)), N = P, A = 0;
                  continue;
                } else if (h.length !== 0) {
                  h = '', v = 0, N = P, A = 0;
                  continue;
                }
              }
              a && (h += h.length > 0 ? `${g}..` : '..', v = 2);
            } else {
              h.length > 0 ? h += `${g}${r.slice(N + 1, P)}` : h = r.slice(N + 1, P), v = P - N - 1;
            }
          }
          N = P, A = 0;
        } else {
          D === p && A !== -1 ? ++A : A = -1;
        }
      }
      return h;
    }

    function S(r, a) {
      l(a, 'pathObject');
      const g = a.dir || a.root,
        m = a.base || `${a.name || ''}${a.ext || ''}`;
      return g ? g === a.root ? `${g}${m}` : `${g}${r}${m}` : m;
    }

    t.win32 = {
      resolve(...r) {
        let a = '',
          g = '',
          m = !1;
        for (let h = r.length - 1; h >= -1; h--) {
          let v;
          if (h >= 0) {
            if (v = r[h], u(v, 'path'), v.length === 0) continue;
          } else {
            a.length === 0 ? v = M.cwd() : (v = M.env[`=${a}`] || M.cwd(), (v === void 0 || v.slice(0, 2)
              .toLowerCase() !== a.toLowerCase() && v.charCodeAt(2) === o) && (v = `${a}\\`));
          }
          const N = v.length;
          let A = 0,
            D = '',
            P = !1;
          const T = v.charCodeAt(0);
          if (N === 1) {
            f(T) && (A = 1, P = !0);
          } else if (f(T)) {
            if (P = !0, f(v.charCodeAt(1))) {
              let I = 2,
                B = I;
              for (; I < N && !f(v.charCodeAt(I));) I++;
              if (I < N && I !== B) {
                const z = v.slice(B, I);
                for (B = I; I < N && f(v.charCodeAt(I));) I++;
                if (I < N && I !== B) {
                  for (B = I; I < N && !f(v.charCodeAt(I));) I++;
                  (I === N || I !== B) && (D = `\\\\${z}\\${v.slice(B, I)}`, A = I);
                }
              }
            } else {
              A = 1;
            }
          } else {
            w(T) && v.charCodeAt(1) === L && (D = v.slice(0, 2), A = 2, N > 2 && f(v.charCodeAt(2)) && (P = !0, A = 3));
          }
          if (D.length > 0) {
            if (a.length > 0) {
              if (D.toLowerCase() !== a.toLowerCase()) continue;
            } else {
              a = D;
            }
          }
          if (m) {
            if (a.length > 0) break;
          } else if (g = `${v.slice(A)}\\${g}`, m = P, P && a.length > 0) break;
        }
        return g = E(g, !m, '\\', f), m ? `${a}\\${g}` : `${a}${g}` || '.';
      },
      normalize(r) {
        u(r, 'path');
        const a = r.length;
        if (a === 0) return '.';
        let g = 0,
          m,
          h = !1;
        const v = r.charCodeAt(0);
        if (a === 1) return y(v) ? '\\' : r;
        if (f(v)) {
          if (h = !0, f(r.charCodeAt(1))) {
            let A = 2,
              D = A;
            for (; A < a && !f(r.charCodeAt(A));) A++;
            if (A < a && A !== D) {
              const P = r.slice(D, A);
              for (D = A; A < a && f(r.charCodeAt(A));) A++;
              if (A < a && A !== D) {
                for (D = A; A < a && !f(r.charCodeAt(A));) A++;
                if (A === a) return `\\\\${P}\\${r.slice(D)}\\`;
                A !== D && (m = `\\\\${P}\\${r.slice(D, A)}`, g = A);
              }
            }
          } else {
            g = 1;
          }
        } else {
          w(v) && r.charCodeAt(1) === L && (m = r.slice(0, 2), g = 2, a > 2 && f(r.charCodeAt(2)) && (h = !0, g = 3));
        }
        let N = g < a ? E(r.slice(g), !h, '\\', f) : '';
        return N.length === 0 && !h && (N = '.'), N.length > 0 && f(r.charCodeAt(a - 1)) && (N += '\\'), m === void 0 ? h ? `\\${N}` : N : h ? `${m}\\${N}` : `${m}${N}`;
      },
      isAbsolute(r) {
        u(r, 'path');
        const a = r.length;
        if (a === 0) return !1;
        const g = r.charCodeAt(0);
        return f(g) || a > 2 && w(g) && r.charCodeAt(1) === L && f(r.charCodeAt(2));
      },
      join(...r) {
        if (r.length === 0) return '.';
        let a,
          g;
        for (let v = 0; v < r.length; ++v) {
          const N = r[v];
          u(N, 'path'), N.length > 0 && (a === void 0 ? a = g = N : a += `\\${N}`);
        }
        if (a === void 0) return '.';
        let m = !0,
          h = 0;
        if (typeof g == 'string' && f(g.charCodeAt(0))) {
          ++h;
          const v = g.length;
          v > 1 && f(g.charCodeAt(1)) && (++h, v > 2 && (f(g.charCodeAt(2)) ? ++h : m = !1));
        }
        if (m) {
          for (; h < a.length && f(a.charCodeAt(h));) h++;
          h >= 2 && (a = `\\${a.slice(h)}`);
        }
        return t.win32.normalize(a);
      },
      relative(r, a) {
        if (u(r, 'from'), u(a, 'to'), r === a) return '';
        const g = t.win32.resolve(r),
          m = t.win32.resolve(a);
        if (g === m || (r = g.toLowerCase(), a = m.toLowerCase(), r === a)) return '';
        let h = 0;
        for (; h < r.length && r.charCodeAt(h) === o;) h++;
        let v = r.length;
        for (; v - 1 > h && r.charCodeAt(v - 1) === o;) v--;
        const N = v - h;
        let A = 0;
        for (; A < a.length && a.charCodeAt(A) === o;) A++;
        let D = a.length;
        for (; D - 1 > A && a.charCodeAt(D - 1) === o;) D--;
        const P = D - A,
          T = N < P ? N : P;
        let I = -1,
          B = 0;
        for (; B < T; B++) {
          const x = r.charCodeAt(h + B);
          if (x !== a.charCodeAt(A + B)) break;
          x === o && (I = B);
        }
        if (B !== T) {
          if (I === -1) return m;
        } else {
          if (P > T) {
            if (a.charCodeAt(A + B) === o) return m.slice(A + B + 1);
            if (B === 2) return m.slice(A + B);
          }
          N > T && (r.charCodeAt(h + B) === o ? I = B : B === 2 && (I = 3)), I === -1 && (I = 0);
        }
        let z = '';
        for (B = h + I + 1; B <= v; ++B) (B === v || r.charCodeAt(B) === o) && (z += z.length === 0 ? '..' : '\\..');
        return A += I, z.length > 0 ? `${z}${m.slice(A, D)}` : (m.charCodeAt(A) === o && ++A, m.slice(A, D));
      },
      toNamespacedPath(r) {
        if (typeof r != 'string' || r.length === 0) return r;
        const a = t.win32.resolve(r);
        if (a.length <= 2) return r;
        if (a.charCodeAt(0) === o) {
          if (a.charCodeAt(1) === o) {
            const g = a.charCodeAt(2);
            if (g !== e && g !== p) return `\\\\?\\UNC\\${a.slice(2)}`;
          }
        } else if (w(a.charCodeAt(0)) && a.charCodeAt(1) === L && a.charCodeAt(2) === o) return `\\\\?\\${a}`;
        return r;
      },
      dirname(r) {
        u(r, 'path');
        const a = r.length;
        if (a === 0) return '.';
        let g = -1,
          m = 0;
        const h = r.charCodeAt(0);
        if (a === 1) return f(h) ? r : '.';
        if (f(h)) {
          if (g = m = 1, f(r.charCodeAt(1))) {
            let A = 2,
              D = A;
            for (; A < a && !f(r.charCodeAt(A));) A++;
            if (A < a && A !== D) {
              for (D = A; A < a && f(r.charCodeAt(A));) A++;
              if (A < a && A !== D) {
                for (D = A; A < a && !f(r.charCodeAt(A));) A++;
                if (A === a) return r;
                A !== D && (g = m = A + 1);
              }
            }
          }
        } else {
          w(h) && r.charCodeAt(1) === L && (g = a > 2 && f(r.charCodeAt(2)) ? 3 : 2, m = g);
        }
        let v = -1,
          N = !0;
        for (let A = a - 1; A >= m; --A) {
          if (f(r.charCodeAt(A))) {
            if (!N) {
              v = A;
              break;
            }
          } else {
            N = !1;
          }
        }
        if (v === -1) {
          if (g === -1) return '.';
          v = g;
        }
        return r.slice(0, v);
      },
      basename(r, a) {
        a !== void 0 && u(a, 'ext'), u(r, 'path');
        let g = 0,
          m = -1,
          h = !0,
          v;
        if (r.length >= 2 && w(r.charCodeAt(0)) && r.charCodeAt(1) === L && (g = 2), a !== void 0 && a.length > 0 && a.length <= r.length) {
          if (a === r) return '';
          let N = a.length - 1,
            A = -1;
          for (v = r.length - 1; v >= g; --v) {
            const D = r.charCodeAt(v);
            if (f(D)) {
              if (!h) {
                g = v + 1;
                break;
              }
            } else {
              A === -1 && (h = !1, A = v + 1), N >= 0 && (D === a.charCodeAt(N) ? --N === -1 && (m = v) : (N = -1, m = A));
            }
          }
          return g === m ? m = A : m === -1 && (m = r.length), r.slice(g, m);
        }
        for (v = r.length - 1; v >= g; --v) {
          if (f(r.charCodeAt(v))) {
            if (!h) {
              g = v + 1;
              break;
            }
          } else {
            m === -1 && (h = !1, m = v + 1);
          }
        }
        return m === -1 ? '' : r.slice(g, m);
      },
      extname(r) {
        u(r, 'path');
        let a = 0,
          g = -1,
          m = 0,
          h = -1,
          v = !0,
          N = 0;
        r.length >= 2 && r.charCodeAt(1) === L && w(r.charCodeAt(0)) && (a = m = 2);
        for (let A = r.length - 1; A >= a; --A) {
          const D = r.charCodeAt(A);
          if (f(D)) {
            if (!v) {
              m = A + 1;
              break;
            }
            continue;
          }
          h === -1 && (v = !1, h = A + 1), D === p ? g === -1 ? g = A : N !== 1 && (N = 1) : g !== -1 && (N = -1);
        }
        return g === -1 || h === -1 || N === 0 || N === 1 && g === h - 1 && g === m + 1 ? '' : r.slice(g, h);
      },
      format: S.bind(null, '\\'),
      parse(r) {
        u(r, 'path');
        const a = {
          root: '',
          dir: '',
          base: '',
          ext: '',
          name: '',
        };
        if (r.length === 0) return a;
        const g = r.length;
        let m = 0,
          h = r.charCodeAt(0);
        if (g === 1) return f(h) ? (a.root = a.dir = r, a) : (a.base = a.name = r, a);
        if (f(h)) {
          if (m = 1, f(r.charCodeAt(1))) {
            let I = 2,
              B = I;
            for (; I < g && !f(r.charCodeAt(I));) I++;
            if (I < g && I !== B) {
              for (B = I; I < g && f(r.charCodeAt(I));) I++;
              if (I < g && I !== B) {
                for (B = I; I < g && !f(r.charCodeAt(I));) I++;
                I === g ? m = I : I !== B && (m = I + 1);
              }
            }
          }
        } else if (w(h) && r.charCodeAt(1) === L) {
          if (g <= 2) return a.root = a.dir = r, a;
          if (m = 2, f(r.charCodeAt(2))) {
            if (g === 3) return a.root = a.dir = r, a;
            m = 3;
          }
        }
        m > 0 && (a.root = r.slice(0, m));
        let v = -1,
          N = m,
          A = -1,
          D = !0,
          P = r.length - 1,
          T = 0;
        for (; P >= m; --P) {
          if (h = r.charCodeAt(P), f(h)) {
            if (!D) {
              N = P + 1;
              break;
            }
            continue;
          }
          A === -1 && (D = !1, A = P + 1), h === p ? v === -1 ? v = P : T !== 1 && (T = 1) : v !== -1 && (T = -1);
        }
        return A !== -1 && (v === -1 || T === 0 || T === 1 && v === A - 1 && v === N + 1 ? a.base = a.name = r.slice(N, A) : (a.name = r.slice(N, v), a.base = r.slice(N, A), a.ext = r.slice(v, A))), N > 0 && N !== m ? a.dir = r.slice(0, N - 1) : a.dir = a.root, a;
      },
      sep: '\\',
      delimiter: ';',
      win32: null,
      posix: null,
    };
    const C = (() => {
      if (b) {
        const r = /\\/g;
        return () => {
          const a = M.cwd()
            .replace(r, '/');
          return a.slice(a.indexOf('/'));
        };
      }
      return () => M.cwd();
    })();
    t.posix = {
      resolve(...r) {
        let a = '',
          g = !1;
        for (let m = r.length - 1; m >= -1 && !g; m--) {
          const h = m >= 0 ? r[m] : C();
          u(h, 'path'), h.length !== 0 && (a = `${h}/${a}`, g = h.charCodeAt(0) === c);
        }
        return a = E(a, !g, '/', y), g ? `/${a}` : a.length > 0 ? a : '.';
      },
      normalize(r) {
        if (u(r, 'path'), r.length === 0) return '.';
        const a = r.charCodeAt(0) === c,
          g = r.charCodeAt(r.length - 1) === c;
        return r = E(r, !a, '/', y), r.length === 0 ? a ? '/' : g ? './' : '.' : (g && (r += '/'), a ? `/${r}` : r);
      },
      isAbsolute(r) {
        return u(r, 'path'), r.length > 0 && r.charCodeAt(0) === c;
      },
      join(...r) {
        if (r.length === 0) return '.';
        let a;
        for (let g = 0; g < r.length; ++g) {
          const m = r[g];
          u(m, 'path'), m.length > 0 && (a === void 0 ? a = m : a += `/${m}`);
        }
        return a === void 0 ? '.' : t.posix.normalize(a);
      },
      relative(r, a) {
        if (u(r, 'from'), u(a, 'to'), r === a || (r = t.posix.resolve(r), a = t.posix.resolve(a), r === a)) return '';
        const g = 1,
          m = r.length,
          h = m - g,
          v = 1,
          N = a.length - v,
          A = h < N ? h : N;
        let D = -1,
          P = 0;
        for (; P < A; P++) {
          const I = r.charCodeAt(g + P);
          if (I !== a.charCodeAt(v + P)) break;
          I === c && (D = P);
        }
        if (P === A) {
          if (N > A) {
            if (a.charCodeAt(v + P) === c) return a.slice(v + P + 1);
            if (P === 0) return a.slice(v + P);
          } else {
            h > A && (r.charCodeAt(g + P) === c ? D = P : P === 0 && (D = 0));
          }
        }
        let T = '';
        for (P = g + D + 1; P <= m; ++P) (P === m || r.charCodeAt(P) === c) && (T += T.length === 0 ? '..' : '/..');
        return `${T}${a.slice(v + D)}`;
      },
      toNamespacedPath(r) {
        return r;
      },
      dirname(r) {
        if (u(r, 'path'), r.length === 0) return '.';
        const a = r.charCodeAt(0) === c;
        let g = -1,
          m = !0;
        for (let h = r.length - 1; h >= 1; --h) {
          if (r.charCodeAt(h) === c) {
            if (!m) {
              g = h;
              break;
            }
          } else {
            m = !1;
          }
        }
        return g === -1 ? a ? '/' : '.' : a && g === 1 ? '//' : r.slice(0, g);
      },
      basename(r, a) {
        a !== void 0 && u(a, 'ext'), u(r, 'path');
        let g = 0,
          m = -1,
          h = !0,
          v;
        if (a !== void 0 && a.length > 0 && a.length <= r.length) {
          if (a === r) return '';
          let N = a.length - 1,
            A = -1;
          for (v = r.length - 1; v >= 0; --v) {
            const D = r.charCodeAt(v);
            if (D === c) {
              if (!h) {
                g = v + 1;
                break;
              }
            } else {
              A === -1 && (h = !1, A = v + 1), N >= 0 && (D === a.charCodeAt(N) ? --N === -1 && (m = v) : (N = -1, m = A));
            }
          }
          return g === m ? m = A : m === -1 && (m = r.length), r.slice(g, m);
        }
        for (v = r.length - 1; v >= 0; --v) {
          if (r.charCodeAt(v) === c) {
            if (!h) {
              g = v + 1;
              break;
            }
          } else {
            m === -1 && (h = !1, m = v + 1);
          }
        }
        return m === -1 ? '' : r.slice(g, m);
      },
      extname(r) {
        u(r, 'path');
        let a = -1,
          g = 0,
          m = -1,
          h = !0,
          v = 0;
        for (let N = r.length - 1; N >= 0; --N) {
          const A = r.charCodeAt(N);
          if (A === c) {
            if (!h) {
              g = N + 1;
              break;
            }
            continue;
          }
          m === -1 && (h = !1, m = N + 1), A === p ? a === -1 ? a = N : v !== 1 && (v = 1) : a !== -1 && (v = -1);
        }
        return a === -1 || m === -1 || v === 0 || v === 1 && a === m - 1 && a === g + 1 ? '' : r.slice(a, m);
      },
      format: S.bind(null, '/'),
      parse(r) {
        u(r, 'path');
        const a = {
          root: '',
          dir: '',
          base: '',
          ext: '',
          name: '',
        };
        if (r.length === 0) return a;
        const g = r.charCodeAt(0) === c;
        let m;
        g ? (a.root = '/', m = 1) : m = 0;
        let h = -1,
          v = 0,
          N = -1,
          A = !0,
          D = r.length - 1,
          P = 0;
        for (; D >= m; --D) {
          const T = r.charCodeAt(D);
          if (T === c) {
            if (!A) {
              v = D + 1;
              break;
            }
            continue;
          }
          N === -1 && (A = !1, N = D + 1), T === p ? h === -1 ? h = D : P !== 1 && (P = 1) : h !== -1 && (P = -1);
        }
        if (N !== -1) {
          const T = v === 0 && g ? 1 : v;
          h === -1 || P === 0 || P === 1 && h === N - 1 && h === v + 1 ? a.base = a.name = r.slice(T, N) : (a.name = r.slice(T, h), a.base = r.slice(T, N), a.ext = r.slice(h, N));
        }
        return v > 0 ? a.dir = r.slice(0, v - 1) : g && (a.dir = '/'), a;
      },
      sep: '/',
      delimiter: ':',
      win32: null,
      posix: null,
    }, t.posix.win32 = t.win32.win32 = t.win32, t.posix.posix = t.win32.posix = t.posix, t.normalize = b ? t.win32.normalize : t.posix.normalize, t.resolve = b ? t.win32.resolve : t.posix.resolve, t.relative = b ? t.win32.relative : t.posix.relative, t.dirname = b ? t.win32.dirname : t.posix.dirname, t.basename = b ? t.win32.basename : t.posix.basename, t.extname = b ? t.win32.extname : t.posix.extname, t.sep = b ? t.win32.sep : t.posix.sep;
  }), X(J[18], Z([0, 1, 63, 17]), function(q, t, M, R) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.uriToFsPath = t.URI = void 0;
    const i = /^\w[\w\d+.-]*$/,
      d = /^\//,
      _ = /^\/\//;

    function p(g, m) {
      if (!g.scheme && m) throw new Error(`[UriError]: Scheme is missing: {scheme: "", authority: "${g.authority}", path: "${g.path}", query: "${g.query}", fragment: "${g.fragment}"}`);
      if (g.scheme && !i.test(g.scheme)) throw new Error('[UriError]: Scheme contains illegal characters.');
      if (g.path) {
        if (g.authority) {
          if (!d.test(g.path)) throw new Error('[UriError]: If a URI contains an authority component, then the path component must either be empty or begin with a slash ("/") character');
        } else if (_.test(g.path)) throw new Error('[UriError]: If a URI does not contain an authority component, then the path cannot begin with two slash characters ("//")');
      }
    }

    function c(g, m) {
      return !g && !m ? 'file' : g;
    }

    function o(g, m) {
      switch (g) {
        case'https':
        case'http':
        case'file':
          m ? m[0] !== e && (m = e + m) : m = e;
          break;
      }
      return m;
    }

    const L = '',
      e = '/',
      s = /^(([^:/?#]+?):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/;

    class l {
      constructor(m, h, v, N, A, D = !1) {
        typeof m == 'object' ? (this.scheme = m.scheme || L, this.authority = m.authority || L, this.path = m.path || L, this.query = m.query || L, this.fragment = m.fragment || L) : (this.scheme = c(m, D), this.authority = h || L, this.path = o(this.scheme, v || L), this.query = N || L, this.fragment = A || L, p(this, D));
      }

      get fsPath() {
        return E(this, !1);
      }

      static isUri(m) {
        return m instanceof l ? !0 : m ? typeof m.authority == 'string' && typeof m.fragment == 'string' && typeof m.path == 'string' && typeof m.query == 'string' && typeof m.scheme == 'string' && typeof m.fsPath == 'string' && typeof m.with == 'function' && typeof m.toString == 'function' : !1;
      }

      static parse(m, h = !1) {
        const v = s.exec(m);
        return v ? new b(v[2] || L, a(v[4] || L), a(v[5] || L), a(v[7] || L), a(v[9] || L), h) : new b(L, L, L, L, L);
      }

      static file(m) {
        let h = L;
        if (R.isWindows && (m = m.replace(/\\/g, e)), m[0] === e && m[1] === e) {
          const v = m.indexOf(e, 2);
          v === -1 ? (h = m.substring(2), m = e) : (h = m.substring(2, v), m = m.substring(v) || e);
        }
        return new b('file', h, m, L, L);
      }

      static from(m, h) {
        return new b(m.scheme, m.authority, m.path, m.query, m.fragment, h);
      }

      static joinPath(m, ...h) {
        if (!m.path) throw new Error('[UriError]: cannot call joinPath on URI without path');
        let v;
        return R.isWindows && m.scheme === 'file' ? v = l.file(M.win32.join(E(m, !0), ...h)).path : v = M.posix.join(m.path, ...h), m.with({ path: v });
      }

      static revive(m) {
        var h,
          v;
        if (m) {
          if (m instanceof l) return m;
          {
            const N = new b(m);
            return N._formatted = (h = m.external) !== null && h !== void 0 ? h : null, N._fsPath = m._sep === u && (v = m.fsPath) !== null && v !== void 0 ? v : null, N;
          }
        } else {
          return m;
        }
      }

      with(m) {
        if (!m) return this;
        let {
          scheme: h,
          authority: v,
          path: N,
          query: A,
          fragment: D,
        } = m;
        return h === void 0 ? h = this.scheme : h === null && (h = L), v === void 0 ? v = this.authority : v === null && (v = L), N === void 0 ? N = this.path : N === null && (N = L), A === void 0 ? A = this.query : A === null && (A = L), D === void 0 ? D = this.fragment : D === null && (D = L), h === this.scheme && v === this.authority && N === this.path && A === this.query && D === this.fragment ? this : new b(h, v, N, A, D);
      }

      toString(m = !1) {
        return S(this, m);
      }

      toJSON() {
        return this;
      }
    }

    t.URI = l;
    const u = R.isWindows ? 1 : void 0;

    class b extends l {
      constructor() {
        super(...arguments), this._formatted = null, this._fsPath = null;
      }

      get fsPath() {
        return this._fsPath || (this._fsPath = E(this, !1)), this._fsPath;
      }

      toString(m = !1) {
        return m ? S(this, !0) : (this._formatted || (this._formatted = S(this, !1)), this._formatted);
      }

      toJSON() {
        const m = { $mid: 1 };
        return this._fsPath && (m.fsPath = this._fsPath, m._sep = u), this._formatted && (m.external = this._formatted), this.path && (m.path = this.path), this.scheme && (m.scheme = this.scheme), this.authority && (m.authority = this.authority), this.query && (m.query = this.query), this.fragment && (m.fragment = this.fragment), m;
      }
    }

    const f = {
      58: '%3A',
      47: '%2F',
      63: '%3F',
      35: '%23',
      91: '%5B',
      93: '%5D',
      64: '%40',
      33: '%21',
      36: '%24',
      38: '%26',
      39: '%27',
      40: '%28',
      41: '%29',
      42: '%2A',
      43: '%2B',
      44: '%2C',
      59: '%3B',
      61: '%3D',
      32: '%20',
    };

    function y(g, m, h) {
      let v,
        N = -1;
      for (let A = 0; A < g.length; A++) {
        const D = g.charCodeAt(A);
        if (D >= 97 && D <= 122 || D >= 65 && D <= 90 || D >= 48 && D <= 57 || D === 45 || D === 46 || D === 95 || D === 126 || m && D === 47 || h && D === 91 || h && D === 93 || h && D === 58) {
          N !== -1 && (v += encodeURIComponent(g.substring(N, A)), N = -1), v !== void 0 && (v += g.charAt(A));
        } else {
          v === void 0 && (v = g.substr(0, A));
          const P = f[D];
          P !== void 0 ? (N !== -1 && (v += encodeURIComponent(g.substring(N, A)), N = -1), v += P) : N === -1 && (N = A);
        }
      }
      return N !== -1 && (v += encodeURIComponent(g.substring(N))), v !== void 0 ? v : g;
    }

    function w(g) {
      let m;
      for (let h = 0; h < g.length; h++) {
        const v = g.charCodeAt(h);
        v === 35 || v === 63 ? (m === void 0 && (m = g.substr(0, h)), m += f[v]) : m !== void 0 && (m += g[h]);
      }
      return m !== void 0 ? m : g;
    }

    function E(g, m) {
      let h;
      return g.authority && g.path.length > 1 && g.scheme === 'file' ? h = `//${g.authority}${g.path}` : g.path.charCodeAt(0) === 47 && (g.path.charCodeAt(1) >= 65 && g.path.charCodeAt(1) <= 90 || g.path.charCodeAt(1) >= 97 && g.path.charCodeAt(1) <= 122) && g.path.charCodeAt(2) === 58 ? m ? h = g.path.substr(1) : h = g.path[1].toLowerCase() + g.path.substr(2) : h = g.path, R.isWindows && (h = h.replace(/\//g, '\\')), h;
    }

    t.uriToFsPath = E;

    function S(g, m) {
      const h = m ? w : y;
      let v = '', {
        scheme: N,
        authority: A,
        path: D,
        query: P,
        fragment: T,
      } = g;
      if (N && (v += N, v += ':'), (A || N === 'file') && (v += e, v += e), A) {
        let I = A.indexOf('@');
        if (I !== -1) {
          const B = A.substr(0, I);
          A = A.substr(I + 1), I = B.lastIndexOf(':'), I === -1 ? v += h(B, !1, !1) : (v += h(B.substr(0, I), !1, !1), v += ':', v += h(B.substr(I + 1), !1, !0)), v += '@';
        }
        A = A.toLowerCase(), I = A.lastIndexOf(':'), I === -1 ? v += h(A, !1, !0) : (v += h(A.substr(0, I), !1, !0), v += A.substr(I));
      }
      if (D) {
        if (D.length >= 3 && D.charCodeAt(0) === 47 && D.charCodeAt(2) === 58) {
          const I = D.charCodeAt(1);
          I >= 65 && I <= 90 && (D = `/${String.fromCharCode(I + 32)}:${D.substr(3)}`);
        } else if (D.length >= 2 && D.charCodeAt(1) === 58) {
          const I = D.charCodeAt(0);
          I >= 65 && I <= 90 && (D = `${String.fromCharCode(I + 32)}:${D.substr(2)}`);
        }
        v += h(D, !0, !1);
      }
      return P && (v += '?', v += h(P, !1, !1)), T && (v += '#', v += m ? T : y(T, !1, !1)), v;
    }

    function C(g) {
      try {
        return decodeURIComponent(g);
      } catch {
        return g.length > 3 ? g.substr(0, 3) + C(g.substr(3)) : g;
      }
    }

    const r = /(%[0-9A-Za-z][0-9A-Za-z])+/g;

    function a(g) {
      return g.match(r) ? g.replace(r, m => C(m)) : g;
    }
  }), X(J[67], Z([0, 1, 5, 9, 13, 14, 17, 6]), function(q, t, M, R, i, d, _, p) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.create = t.SimpleWorkerServer = t.SimpleWorkerClient = t.logOnceWebWorkerWarning = void 0;
    const c = '$initialize';
    let o = !1;

    function L(a) {
      _.isWeb && (o || (o = !0, console.warn('Could not create web worker(s). Falling back to loading web worker code in main thread, which might cause UI freezes. Please see https://github.com/microsoft/monaco-editor#faq')), console.warn(a.message));
    }

    t.logOnceWebWorkerWarning = L;

    class e {
      constructor(g, m, h, v) {
        this.vsWorker = g, this.req = m, this.method = h, this.args = v, this.type = 0;
      }
    }

    class s {
      constructor(g, m, h, v) {
        this.vsWorker = g, this.seq = m, this.res = h, this.err = v, this.type = 1;
      }
    }

    class l {
      constructor(g, m, h, v) {
        this.vsWorker = g, this.req = m, this.eventName = h, this.arg = v, this.type = 2;
      }
    }

    class u {
      constructor(g, m, h) {
        this.vsWorker = g, this.req = m, this.event = h, this.type = 3;
      }
    }

    class b {
      constructor(g, m) {
        this.vsWorker = g, this.req = m, this.type = 4;
      }
    }

    class f {
      constructor(g) {
        this._workerId = -1, this._handler = g, this._lastSentReq = 0, this._pendingReplies = Object.create(null), this._pendingEmitters = new Map, this._pendingEvents = new Map;
      }

      setWorkerId(g) {
        this._workerId = g;
      }

      sendMessage(g, m) {
        const h = String(++this._lastSentReq);
        return new Promise((v, N) => {
          this._pendingReplies[h] = {
            resolve: v,
            reject: N,
          }, this._send(new e(this._workerId, h, g, m));
        });
      }

      listen(g, m) {
        let h = null;
        const v = new R.Emitter({
          onWillAddFirstListener: () => {
            h = String(++this._lastSentReq), this._pendingEmitters.set(h, v), this._send(new l(this._workerId, h, g, m));
          },
          onDidRemoveLastListener: () => {
            this._pendingEmitters.delete(h), this._send(new b(this._workerId, h)), h = null;
          },
        });
        return v.event;
      }

      handleMessage(g) {
        !g || !g.vsWorker || this._workerId !== -1 && g.vsWorker !== this._workerId || this._handleMessage(g);
      }

      _handleMessage(g) {
        switch (g.type) {
          case 1:
            return this._handleReplyMessage(g);
          case 0:
            return this._handleRequestMessage(g);
          case 2:
            return this._handleSubscribeEventMessage(g);
          case 3:
            return this._handleEventMessage(g);
          case 4:
            return this._handleUnsubscribeEventMessage(g);
        }
      }

      _handleReplyMessage(g) {
        if (!this._pendingReplies[g.seq]) {
          console.warn('Got reply to unknown seq');
          return;
        }
        const m = this._pendingReplies[g.seq];
        if (delete this._pendingReplies[g.seq], g.err) {
          let h = g.err;
          g.err.$isError && (h = new Error, h.name = g.err.name, h.message = g.err.message, h.stack = g.err.stack), m.reject(h);
          return;
        }
        m.resolve(g.res);
      }

      _handleRequestMessage(g) {
        const m = g.req;
        this._handler.handleMessage(g.method, g.args)
          .then(v => {
            this._send(new s(this._workerId, m, v, void 0));
          }, v => {
            v.detail instanceof Error && (v.detail = (0, M.transformErrorForSerialization)(v.detail)), this._send(new s(this._workerId, m, void 0, (0, M.transformErrorForSerialization)(v)));
          });
      }

      _handleSubscribeEventMessage(g) {
        const m = g.req,
          h = this._handler.handleEvent(g.eventName, g.arg)(v => {
            this._send(new u(this._workerId, m, v));
          });
        this._pendingEvents.set(m, h);
      }

      _handleEventMessage(g) {
        if (!this._pendingEmitters.has(g.req)) {
          console.warn('Got event for unknown req');
          return;
        }
        this._pendingEmitters.get(g.req)
          .fire(g.event);
      }

      _handleUnsubscribeEventMessage(g) {
        if (!this._pendingEvents.has(g.req)) {
          console.warn('Got unsubscribe for unknown req');
          return;
        }
        this._pendingEvents.get(g.req)
          .dispose(), this._pendingEvents.delete(g.req);
      }

      _send(g) {
        const m = [];
        if (g.type === 0) for (let h = 0; h < g.args.length; h++) g.args[h] instanceof ArrayBuffer && m.push(g.args[h]); else g.type === 1 && g.res instanceof ArrayBuffer && m.push(g.res);
        this._handler.sendMessage(g, m);
      }
    }

    class y extends i.Disposable {
      constructor(g, m, h) {
        super();
        let v = null;
        this._worker = this._register(g.create('vs/base/common/worker/simpleWorker', I => {
          this._protocol.handleMessage(I);
        }, I => {
          v?.(I);
        })), this._protocol = new f({
          sendMessage: (I, B) => {
            this._worker.postMessage(I, B);
          },
          handleMessage: (I, B) => {
            if (typeof h[I] != 'function') return Promise.reject(new Error('Missing method ' + I + ' on main thread host.'));
            try {
              return Promise.resolve(h[I].apply(h, B));
            } catch (z) {
              return Promise.reject(z);
            }
          },
          handleEvent: (I, B) => {
            if (E(I)) {
              const z = h[I].call(h, B);
              if (typeof z != 'function') throw new Error(`Missing dynamic event ${I} on main thread host.`);
              return z;
            }
            if (w(I)) {
              const z = h[I];
              if (typeof z != 'function') throw new Error(`Missing event ${I} on main thread host.`);
              return z;
            }
            throw new Error(`Malformed event name ${I}`);
          },
        }), this._protocol.setWorkerId(this._worker.getId());
        let N = null;
        const A = globalThis.require;
        typeof A < 'u' && typeof A.getConfig == 'function' ? N = A.getConfig() : typeof globalThis.requirejs < 'u' && (N = globalThis.requirejs.s.contexts._.config);
        const D = (0, d.getAllMethodNames)(h);
        this._onModuleLoaded = this._protocol.sendMessage(c, [this._worker.getId(), JSON.parse(JSON.stringify(N)), m, D]);
        const P = (I, B) => this._request(I, B),
          T = (I, B) => this._protocol.listen(I, B);
        this._lazyProxy = new Promise((I, B) => {
          v = B, this._onModuleLoaded.then(z => {
            I(S(z, P, T));
          }, z => {
            B(z), this._onError('Worker failed to load ' + m, z);
          });
        });
      }

      getProxyObject() {
        return this._lazyProxy;
      }

      _request(g, m) {
        return new Promise((h, v) => {
          this._onModuleLoaded.then(() => {
            this._protocol.sendMessage(g, m)
              .then(h, v);
          }, v);
        });
      }

      _onError(g, m) {
        console.error(g), console.info(m);
      }
    }

    t.SimpleWorkerClient = y;

    function w(a) {
      return a[0] === 'o' && a[1] === 'n' && p.isUpperAsciiLetter(a.charCodeAt(2));
    }

    function E(a) {
      return /^onDynamic/.test(a) && p.isUpperAsciiLetter(a.charCodeAt(9));
    }

    function S(a, g, m) {
      const h = A => function() {
          const D = Array.prototype.slice.call(arguments, 0);
          return g(A, D);
        },
        v = A => function(D) {
          return m(A, D);
        },
        N = {};
      for (const A of a) {
        if (E(A)) {
          N[A] = v(A);
          continue;
        }
        if (w(A)) {
          N[A] = m(A, void 0);
          continue;
        }
        N[A] = h(A);
      }
      return N;
    }

    class C {
      constructor(g, m) {
        this._requestHandlerFactory = m, this._requestHandler = null, this._protocol = new f({
          sendMessage: (h, v) => {
            g(h, v);
          },
          handleMessage: (h, v) => this._handleMessage(h, v),
          handleEvent: (h, v) => this._handleEvent(h, v),
        });
      }

      onmessage(g) {
        this._protocol.handleMessage(g);
      }

      _handleMessage(g, m) {
        if (g === c) return this.initialize(m[0], m[1], m[2], m[3]);
        if (!this._requestHandler || typeof this._requestHandler[g] != 'function') return Promise.reject(new Error('Missing requestHandler or method: ' + g));
        try {
          return Promise.resolve(this._requestHandler[g].apply(this._requestHandler, m));
        } catch (h) {
          return Promise.reject(h);
        }
      }

      _handleEvent(g, m) {
        if (!this._requestHandler) throw new Error('Missing requestHandler');
        if (E(g)) {
          const h = this._requestHandler[g].call(this._requestHandler, m);
          if (typeof h != 'function') throw new Error(`Missing dynamic event ${g} on request handler.`);
          return h;
        }
        if (w(g)) {
          const h = this._requestHandler[g];
          if (typeof h != 'function') throw new Error(`Missing event ${g} on request handler.`);
          return h;
        }
        throw new Error(`Malformed event name ${g}`);
      }

      initialize(g, m, h, v) {
        this._protocol.setWorkerId(g);
        const D = S(v, (P, T) => this._protocol.sendMessage(P, T), (P, T) => this._protocol.listen(P, T));
        return this._requestHandlerFactory ? (this._requestHandler = this._requestHandlerFactory(D), Promise.resolve((0, d.getAllMethodNames)(this._requestHandler))) : (m && (typeof m.baseUrl < 'u' && delete m.baseUrl, typeof m.paths < 'u' && typeof m.paths.vs < 'u' && delete m.paths.vs, typeof m.trustedTypesPolicy < 'u' && delete m.trustedTypesPolicy, m.catchError = !0, globalThis.require.config(m)), new Promise((P, T) => {
          (globalThis.require || q)([h], B => {
            if (this._requestHandler = B.create(D), !this._requestHandler) {
              T(new Error('No RequestHandler!'));
              return;
            }
            P((0, d.getAllMethodNames)(this._requestHandler));
          }, T);
        }));
      }
    }

    t.SimpleWorkerServer = C;

    function r(a) {
      return new C(a, null);
    }

    t.create = r;
  }), X(J[64], Z([19, 61]), function(q, t) {
    return q.create('vs/editor/common/languages', t);
  }), X(J[65], Z([0, 1, 40, 18, 2, 59, 64]), function(q, t, M, R, i, d, _) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.InlineEditTriggerKind = t.TokenizationRegistry = t.LazyTokenizationSupport = t.InlayHintKind = t.Command = t.NewSymbolNameTag = t.FoldingRangeKind = t.TextEdit = t.SymbolKinds = t.getAriaLabelForSymbol = t.symbolKindNames = t.isLocationLink = t.DocumentHighlightKind = t.SignatureHelpTriggerKind = t.SelectedSuggestionInfo = t.InlineCompletionTriggerKind = t.CompletionItemKinds = t.EncodedTokenizationResult = t.TokenizationResult = t.Token = void 0;

    class p {
      constructor(h, v, N) {
        this.offset = h, this.type = v, this.language = N, this._tokenBrand = void 0;
      }

      toString() {
        return '(' + this.offset + ', ' + this.type + ')';
      }
    }

    t.Token = p;

    class c {
      constructor(h, v) {
        this.tokens = h, this.endState = v, this._tokenizationResultBrand = void 0;
      }
    }

    t.TokenizationResult = c;

    class o {
      constructor(h, v) {
        this.tokens = h, this.endState = v, this._encodedTokenizationResultBrand = void 0;
      }
    }

    t.EncodedTokenizationResult = o;
    var L;
    (function(m) {
      const h = new Map;
      h.set(0, M.Codicon.symbolMethod), h.set(1, M.Codicon.symbolFunction), h.set(2, M.Codicon.symbolConstructor), h.set(3, M.Codicon.symbolField), h.set(4, M.Codicon.symbolVariable), h.set(5, M.Codicon.symbolClass), h.set(6, M.Codicon.symbolStruct), h.set(7, M.Codicon.symbolInterface), h.set(8, M.Codicon.symbolModule), h.set(9, M.Codicon.symbolProperty), h.set(10, M.Codicon.symbolEvent), h.set(11, M.Codicon.symbolOperator), h.set(12, M.Codicon.symbolUnit), h.set(13, M.Codicon.symbolValue), h.set(15, M.Codicon.symbolEnum), h.set(14, M.Codicon.symbolConstant), h.set(15, M.Codicon.symbolEnum), h.set(16, M.Codicon.symbolEnumMember), h.set(17, M.Codicon.symbolKeyword), h.set(27, M.Codicon.symbolSnippet), h.set(18, M.Codicon.symbolText), h.set(19, M.Codicon.symbolColor), h.set(20, M.Codicon.symbolFile), h.set(21, M.Codicon.symbolReference), h.set(22, M.Codicon.symbolCustomColor), h.set(23, M.Codicon.symbolFolder), h.set(24, M.Codicon.symbolTypeParameter), h.set(25, M.Codicon.account), h.set(26, M.Codicon.issues);

      function v(D) {
        let P = h.get(D);
        return P || (console.info('No codicon found for CompletionItemKind ' + D), P = M.Codicon.symbolProperty), P;
      }

      m.toIcon = v;
      const N = new Map;
      N.set('method', 0), N.set('function', 1), N.set('constructor', 2), N.set('field', 3), N.set('variable', 4), N.set('class', 5), N.set('struct', 6), N.set('interface', 7), N.set('module', 8), N.set('property', 9), N.set('event', 10), N.set('operator', 11), N.set('unit', 12), N.set('value', 13), N.set('constant', 14), N.set('enum', 15), N.set('enum-member', 16), N.set('enumMember', 16), N.set('keyword', 17), N.set('snippet', 27), N.set('text', 18), N.set('color', 19), N.set('file', 20), N.set('reference', 21), N.set('customcolor', 22), N.set('folder', 23), N.set('type-parameter', 24), N.set('typeParameter', 24), N.set('account', 25), N.set('issue', 26);

      function A(D, P) {
        let T = N.get(D);
        return typeof T > 'u' && !P && (T = 9), T;
      }

      m.fromString = A;
    })(L || (t.CompletionItemKinds = L = {}));
    var e;
    (function(m) {
      m[m.Automatic = 0] = 'Automatic', m[m.Explicit = 1] = 'Explicit';
    })(e || (t.InlineCompletionTriggerKind = e = {}));

    class s {
      constructor(h, v, N, A) {
        this.range = h, this.text = v, this.completionKind = N, this.isSnippetText = A;
      }

      equals(h) {
        return i.Range.lift(this.range)
          .equalsRange(h.range) && this.text === h.text && this.completionKind === h.completionKind && this.isSnippetText === h.isSnippetText;
      }
    }

    t.SelectedSuggestionInfo = s;
    var l;
    (function(m) {
      m[m.Invoke = 1] = 'Invoke', m[m.TriggerCharacter = 2] = 'TriggerCharacter', m[m.ContentChange = 3] = 'ContentChange';
    })(l || (t.SignatureHelpTriggerKind = l = {}));
    var u;
    (function(m) {
      m[m.Text = 0] = 'Text', m[m.Read = 1] = 'Read', m[m.Write = 2] = 'Write';
    })(u || (t.DocumentHighlightKind = u = {}));

    function b(m) {
      return m && R.URI.isUri(m.uri) && i.Range.isIRange(m.range) && (i.Range.isIRange(m.originSelectionRange) || i.Range.isIRange(m.targetSelectionRange));
    }

    t.isLocationLink = b, t.symbolKindNames = {
      17: (0, _.localize)(0, null),
      16: (0, _.localize)(1, null),
      4: (0, _.localize)(2, null),
      13: (0, _.localize)(3, null),
      8: (0, _.localize)(4, null),
      9: (0, _.localize)(5, null),
      21: (0, _.localize)(6, null),
      23: (0, _.localize)(7, null),
      7: (0, _.localize)(8, null),
      0: (0, _.localize)(9, null),
      11: (0, _.localize)(10, null),
      10: (0, _.localize)(11, null),
      19: (0, _.localize)(12, null),
      5: (0, _.localize)(13, null),
      1: (0, _.localize)(14, null),
      2: (0, _.localize)(15, null),
      20: (0, _.localize)(16, null),
      15: (0, _.localize)(17, null),
      18: (0, _.localize)(18, null),
      24: (0, _.localize)(19, null),
      3: (0, _.localize)(20, null),
      6: (0, _.localize)(21, null),
      14: (0, _.localize)(22, null),
      22: (0, _.localize)(23, null),
      25: (0, _.localize)(24, null),
      12: (0, _.localize)(25, null),
    };

    function f(m, h) {
      return (0, _.localize)(26, null, m, t.symbolKindNames[h]);
    }

    t.getAriaLabelForSymbol = f;
    var y;
    (function(m) {
      const h = new Map;
      h.set(0, M.Codicon.symbolFile), h.set(1, M.Codicon.symbolModule), h.set(2, M.Codicon.symbolNamespace), h.set(3, M.Codicon.symbolPackage), h.set(4, M.Codicon.symbolClass), h.set(5, M.Codicon.symbolMethod), h.set(6, M.Codicon.symbolProperty), h.set(7, M.Codicon.symbolField), h.set(8, M.Codicon.symbolConstructor), h.set(9, M.Codicon.symbolEnum), h.set(10, M.Codicon.symbolInterface), h.set(11, M.Codicon.symbolFunction), h.set(12, M.Codicon.symbolVariable), h.set(13, M.Codicon.symbolConstant), h.set(14, M.Codicon.symbolString), h.set(15, M.Codicon.symbolNumber), h.set(16, M.Codicon.symbolBoolean), h.set(17, M.Codicon.symbolArray), h.set(18, M.Codicon.symbolObject), h.set(19, M.Codicon.symbolKey), h.set(20, M.Codicon.symbolNull), h.set(21, M.Codicon.symbolEnumMember), h.set(22, M.Codicon.symbolStruct), h.set(23, M.Codicon.symbolEvent), h.set(24, M.Codicon.symbolOperator), h.set(25, M.Codicon.symbolTypeParameter);

      function v(N) {
        let A = h.get(N);
        return A || (console.info('No codicon found for SymbolKind ' + N), A = M.Codicon.symbolProperty), A;
      }

      m.toIcon = v;
    })(y || (t.SymbolKinds = y = {}));

    class w {
    }

    t.TextEdit = w;

    class E {
      constructor(h) {
        this.value = h;
      }

      static fromValue(h) {
        switch (h) {
          case'comment':
            return E.Comment;
          case'imports':
            return E.Imports;
          case'region':
            return E.Region;
        }
        return new E(h);
      }
    }

    t.FoldingRangeKind = E, E.Comment = new E('comment'), E.Imports = new E('imports'), E.Region = new E('region');
    var S;
    (function(m) {
      m[m.AIGenerated = 1] = 'AIGenerated';
    })(S || (t.NewSymbolNameTag = S = {}));
    var C;
    (function(m) {
      function h(v) {
        return !v || typeof v != 'object' ? !1 : typeof v.id == 'string' && typeof v.title == 'string';
      }

      m.is = h;
    })(C || (t.Command = C = {}));
    var r;
    (function(m) {
      m[m.Type = 1] = 'Type', m[m.Parameter = 2] = 'Parameter';
    })(r || (t.InlayHintKind = r = {}));

    class a {
      constructor(h) {
        this.createSupport = h, this._tokenizationSupport = null;
      }

      get tokenizationSupport() {
        return this._tokenizationSupport || (this._tokenizationSupport = this.createSupport()), this._tokenizationSupport;
      }

      dispose() {
        this._tokenizationSupport && this._tokenizationSupport.then(h => {
          h && h.dispose();
        });
      }
    }

    t.LazyTokenizationSupport = a, t.TokenizationRegistry = new d.TokenizationRegistry;
    var g;
    (function(m) {
      m[m.Invoke = 0] = 'Invoke', m[m.Automatic = 1] = 'Automatic';
    })(g || (t.InlineEditTriggerKind = g = {}));
  }), X(J[66], Z([0, 1, 38, 9, 35, 18, 4, 2, 41, 65, 58]), function(q, t, M, R, i, d, _, p, c, o, L) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.createMonacoBaseAPI = t.KeyMod = void 0;

    class e {
      static chord(u, b) {
        return (0, i.KeyChord)(u, b);
      }
    }

    t.KeyMod = e, e.CtrlCmd = 2048, e.Shift = 1024, e.Alt = 512, e.WinCtrl = 256;

    function s() {
      return {
        editor: void 0,
        languages: void 0,
        CancellationTokenSource: M.CancellationTokenSource,
        Emitter: R.Emitter,
        KeyCode: L.KeyCode,
        KeyMod: e,
        Position: _.Position,
        Range: p.Range,
        Selection: c.Selection,
        SelectionDirection: L.SelectionDirection,
        MarkerSeverity: L.MarkerSeverity,
        MarkerTag: L.MarkerTag,
        Uri: d.URI,
        Token: o.Token,
      };
    }

    t.createMonacoBaseAPI = s;
  }), X(J[68], Z([0, 1, 24, 18, 4, 2, 55, 28, 51, 52, 66, 23, 57, 49, 14, 50]), function(q, t, M, R, i, d, _, p, c, o, L, e, s, l, u, b) {
    'use strict';
    Object.defineProperty(t, '__esModule', { value: !0 }), t.create = t.EditorSimpleWorker = void 0;

    class f extends _.MirrorTextModel {
      get uri() {
        return this._uri;
      }

      get eol() {
        return this._eol;
      }

      getValue() {
        return this.getText();
      }

      findMatches(S) {
        const C = [];
        for (let r = 0; r < this._lines.length; r++) {
          const a = this._lines[r],
            g = this.offsetAt(new i.Position(r + 1, 1)),
            m = a.matchAll(S);
          for (const h of m) (h.index || h.index === 0) && (h.index = h.index + g), C.push(h);
        }
        return C;
      }

      getLinesContent() {
        return this._lines.slice(0);
      }

      getLineCount() {
        return this._lines.length;
      }

      getLineContent(S) {
        return this._lines[S - 1];
      }

      getWordAtPosition(S, C) {
        const r = (0, p.getWordAtText)(S.column, (0, p.ensureValidWordDefinition)(C), this._lines[S.lineNumber - 1], 0);
        return r ? new d.Range(S.lineNumber, r.startColumn, S.lineNumber, r.endColumn) : null;
      }

      words(S) {
        const C = this._lines,
          r = this._wordenize.bind(this);
        let a = 0,
          g = '',
          m = 0,
          h = [];
        return {
          * [Symbol.iterator]() {
            for (; ;) {
              if (m < h.length) {
                const v = g.substring(h[m].start, h[m].end);
                m += 1, yield v;
              } else if (a < C.length) g = C[a], h = r(g, S), m = 0, a += 1; else break;
            }
          },
        };
      }

      getLineWords(S, C) {
        const r = this._lines[S - 1],
          a = this._wordenize(r, C),
          g = [];
        for (const m of a) {
          g.push({
            word: r.substring(m.start, m.end),
            startColumn: m.start + 1,
            endColumn: m.end + 1,
          });
        }
        return g;
      }

      _wordenize(S, C) {
        const r = [];
        let a;
        for (C.lastIndex = 0; (a = C.exec(S)) && a[0].length !== 0;) {
          r.push({
            start: a.index,
            end: a.index + a[0].length,
          });
        }
        return r;
      }

      getValueInRange(S) {
        if (S = this._validateRange(S), S.startLineNumber === S.endLineNumber) return this._lines[S.startLineNumber - 1].substring(S.startColumn - 1, S.endColumn - 1);
        const C = this._eol,
          r = S.startLineNumber - 1,
          a = S.endLineNumber - 1,
          g = [];
        g.push(this._lines[r].substring(S.startColumn - 1));
        for (let m = r + 1; m < a; m++) g.push(this._lines[m]);
        return g.push(this._lines[a].substring(0, S.endColumn - 1)), g.join(C);
      }

      offsetAt(S) {
        return S = this._validatePosition(S), this._ensureLineStarts(), this._lineStarts.getPrefixSum(S.lineNumber - 2) + (S.column - 1);
      }

      positionAt(S) {
        S = Math.floor(S), S = Math.max(0, S), this._ensureLineStarts();
        const C = this._lineStarts.getIndexOf(S),
          r = this._lines[C.index].length;
        return {
          lineNumber: 1 + C.index,
          column: 1 + Math.min(C.remainder, r),
        };
      }

      _validateRange(S) {
        const C = this._validatePosition({
            lineNumber: S.startLineNumber,
            column: S.startColumn,
          }),
          r = this._validatePosition({
            lineNumber: S.endLineNumber,
            column: S.endColumn,
          });
        return C.lineNumber !== S.startLineNumber || C.column !== S.startColumn || r.lineNumber !== S.endLineNumber || r.column !== S.endColumn ? {
          startLineNumber: C.lineNumber,
          startColumn: C.column,
          endLineNumber: r.lineNumber,
          endColumn: r.column,
        } : S;
      }

      _validatePosition(S) {
        if (!i.Position.isIPosition(S)) throw new Error('bad position');
        let {
            lineNumber: C,
            column: r,
          } = S,
          a = !1;
        if (C < 1) {
          C = 1, r = 1, a = !0;
        } else if (C > this._lines.length) {
          C = this._lines.length, r = this._lines[C - 1].length + 1, a = !0;
        } else {
          const g = this._lines[C - 1].length + 1;
          r < 1 ? (r = 1, a = !0) : r > g && (r = g, a = !0);
        }
        return a ? {
          lineNumber: C,
          column: r,
        } : S;
      }
    }

    class y {
      constructor(S, C) {
        this._host = S, this._models = Object.create(null), this._foreignModuleFactory = C, this._foreignModule = null;
      }

      static computeDiff(S, C, r, a) {
        const g = a === 'advanced' ? l.linesDiffComputers.getDefault() : l.linesDiffComputers.getLegacy(),
          m = S.getLinesContent(),
          h = C.getLinesContent(),
          v = g.computeDiff(m, h, r),
          N = v.changes.length > 0 ? !1 : this._modelsAreIdentical(S, C);

        function A(D) {
          return D.map(P => {
            var T;
            return [P.original.startLineNumber, P.original.endLineNumberExclusive, P.modified.startLineNumber, P.modified.endLineNumberExclusive, (T = P.innerChanges) === null || T === void 0 ? void 0 : T.map(I => [I.originalRange.startLineNumber, I.originalRange.startColumn, I.originalRange.endLineNumber, I.originalRange.endColumn, I.modifiedRange.startLineNumber, I.modifiedRange.startColumn, I.modifiedRange.endLineNumber, I.modifiedRange.endColumn])];
          });
        }

        return {
          identical: N,
          quitEarly: v.hitTimeout,
          changes: A(v.changes),
          moves: v.moves.map(D => [D.lineRangeMapping.original.startLineNumber, D.lineRangeMapping.original.endLineNumberExclusive, D.lineRangeMapping.modified.startLineNumber, D.lineRangeMapping.modified.endLineNumberExclusive, A(D.changes)]),
        };
      }

      static _modelsAreIdentical(S, C) {
        const r = S.getLineCount(),
          a = C.getLineCount();
        if (r !== a) return !1;
        for (let g = 1; g <= r; g++) {
          const m = S.getLineContent(g),
            h = C.getLineContent(g);
          if (m !== h) return !1;
        }
        return !0;
      }

      dispose() {
        this._models = Object.create(null);
      }

      _getModel(S) {
        return this._models[S];
      }

      _getModels() {
        const S = [];
        return Object.keys(this._models)
          .forEach(C => S.push(this._models[C])), S;
      }

      acceptNewModel(S) {
        this._models[S.url] = new f(R.URI.parse(S.url), S.lines, S.EOL, S.versionId);
      }

      acceptModelChanged(S, C) {
        if (!this._models[S]) return;
        this._models[S].onEvents(C);
      }

      acceptRemovedModel(S) {
        this._models[S] && delete this._models[S];
      }

      async computeUnicodeHighlights(S, C, r) {
        const a = this._getModel(S);
        return a ? s.UnicodeTextModelHighlighter.computeUnicodeHighlights(a, C, r) : {
          ranges: [],
          hasMore: !1,
          ambiguousCharacterCount: 0,
          invisibleCharacterCount: 0,
          nonBasicAsciiCharacterCount: 0,
        };
      }

      async computeDiff(S, C, r, a) {
        const g = this._getModel(S),
          m = this._getModel(C);
        return !g || !m ? null : y.computeDiff(g, m, r, a);
      }

      async computeMoreMinimalEdits(S, C, r) {
        const a = this._getModel(S);
        if (!a) return C;
        const g = [];
        let m;
        C = C.slice(0)
          .sort((v, N) => {
            if (v.range && N.range) return d.Range.compareRangesUsingStarts(v.range, N.range);
            const A = v.range ? 0 : 1,
              D = N.range ? 0 : 1;
            return A - D;
          });
        let h = 0;
        for (let v = 1; v < C.length; v++) {
          d.Range.getEndPosition(C[h].range)
            .equals(d.Range.getStartPosition(C[v].range)) ? (C[h].range = d.Range.fromPositions(d.Range.getStartPosition(C[h].range), d.Range.getEndPosition(C[v].range)), C[h].text += C[v].text) : (h++, C[h] = C[v]);
        }
        C.length = h + 1;
        for (let {
          range: v,
          text: N,
          eol: A
        } of C) {
          if (typeof A == 'number' && (m = A), d.Range.isEmpty(v) && !N) continue;
          const D = a.getValueInRange(v);
          if (N = N.replace(/\r\n|\n|\r/g, a.eol), D === N) continue;
          if (Math.max(N.length, D.length) > y._diffLimit) {
            g.push({
              range: v,
              text: N,
            });
            continue;
          }
          const P = (0, M.stringDiff)(D, N, r),
            T = a.offsetAt(d.Range.lift(v)
              .getStartPosition());
          for (const I of P) {
            const B = a.positionAt(T + I.originalStart),
              z = a.positionAt(T + I.originalStart + I.originalLength),
              x = {
                text: N.substr(I.modifiedStart, I.modifiedLength),
                range: {
                  startLineNumber: B.lineNumber,
                  startColumn: B.column,
                  endLineNumber: z.lineNumber,
                  endColumn: z.column,
                },
              };
            a.getValueInRange(x.range) !== x.text && g.push(x);
          }
        }
        return typeof m == 'number' && g.push({
          eol: m,
          text: '',
          range: {
            startLineNumber: 0,
            startColumn: 0,
            endLineNumber: 0,
            endColumn: 0,
          },
        }), g;
      }

      async computeLinks(S) {
        const C = this._getModel(S);
        return C ? (0, c.computeLinks)(C) : null;
      }

      async computeDefaultDocumentColors(S) {
        const C = this._getModel(S);
        return C ? (0, b.computeDefaultDocumentColors)(C) : null
      }

      async textualSuggest(S, C, r, a) {
        const g = new e.StopWatch,
          m = new RegExp(r, a),
          h = new Set;
        e:for (const v of S) {
          const N = this._getModel(v);
          if (N) {
            for (const A of N.words(m)) if (!(A === C || !isNaN(Number(A))) && (h.add(A), h.size > y._suggestionsLimit)) break e
          }
        }
        return {
          words: Array.from(h),
          duration: g.elapsed()
        }
      }

      async computeWordRanges(S, C, r, a) {
        const g = this._getModel(S);
        if (!g) return Object.create(null);
        const m = new RegExp(r, a),
          h = Object.create(null);
        for (let v = C.startLineNumber; v < C.endLineNumber; v++) {
          const N = g.getLineWords(v, m);
          for (const A of N) {
            if (!isNaN(Number(A.word))) continue;
            let D = h[A.word];
            D || (D = [], h[A.word] = D), D.push({
              startLineNumber: v,
              startColumn: A.startColumn,
              endLineNumber: v,
              endColumn: A.endColumn
            })
          }
        }
        return h
      }

      async navigateValueSet(S, C, r, a, g) {
        const m = this._getModel(S);
        if (!m) return null;
        const h = new RegExp(a, g);
        C.startColumn === C.endColumn && (C = {
          startLineNumber: C.startLineNumber,
          startColumn: C.startColumn,
          endLineNumber: C.endLineNumber,
          endColumn: C.endColumn + 1
        });
        const v = m.getValueInRange(C),
          N = m.getWordAtPosition({
            lineNumber: C.startLineNumber,
            column: C.startColumn
          }, h);
        if (!N) return null;
        const A = m.getValueInRange(N);
        return o.BasicInplaceReplace.INSTANCE.navigateValueSet(C, v, N, A, r)
      }

      loadForeignModule(S, C, r) {
        const a = (h, v) => this._host.fhr(h, v),
          m = {
            host: (0, u.createProxyObject)(r, a),
            getMirrorModels: () => this._getModels()
          };
        return this._foreignModuleFactory ? (this._foreignModule = this._foreignModuleFactory(m, C), Promise.resolve((0, u.getAllMethodNames)(this._foreignModule))) : new Promise((h, v) => {
          q([S], N => {
            this._foreignModule = N.create(m, C), h((0, u.getAllMethodNames)(this._foreignModule))
          }, v)
        })
      }

      fmr(S, C) {
        if (!this._foreignModule || typeof this._foreignModule[S] != "function") return Promise.reject(new Error("Missing requestHandler or method: " + S));
        try {
          return Promise.resolve(this._foreignModule[S].apply(this._foreignModule, C))
        } catch (r) {
          return Promise.reject(r)
        }
      }
    }

    t.EditorSimpleWorker = y, y._diffLimit = 1e5, y._suggestionsLimit = 1e4;

    function w(E) {
      return new y(E, null)
    }

    t.create = w, typeof importScripts == "function" && (globalThis.monaco = (0, L.createMonacoBaseAPI)())
  })
}).call(this);

//# sourceMappingURL=../../../../min-maps/vs/base/worker/workerMain.js.map

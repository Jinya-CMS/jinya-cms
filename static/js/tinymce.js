class ConflictError extends Error {
    constructor(error) {
        super(error);
    }
}

const host = JSON.parse(localStorage.getItem("/jinya/api/host"));

function send(verb, url, data, contentType, additionalHeaders = {}, plain = false) {
    const headers = {'Content-Type': contentType, ...additionalHeaders};
    headers.JinyaApiKey =JSON.parse(localStorage.getItem('/jinya/api/key'));

    const request = {
        headers,
        credentials: 'same-origin',
        method: verb,
    };

    if (data) {
        if (data instanceof Blob) {
            request.body = data;
        } else {
            request.body = JSON.stringify(data);
        }
    }

    return fetch(url, request).then(async (response) => {
        if (response.ok) {
            if (response.status !== 204) {
                if (plain) {
                    return response.text();
                }
                return response.json();
            }

            return null;
        }

        const httpError = await response.json().then((error) => error.error);

        throw new ConflictError(httpError.toString());
    });
}

const JinyaRequest = {
    get(url) {
        return send('get', `${host}${url}`);
    },
    getPlain(url) {
        return send('get', `${host}${url}`, null, null, null, true);
    },
    head(url) {
        return send('head', `${host}${url}`);
    },
    put(url, data) {
        return send('put', `${host}${url}`, data, 'application/json');
    },
    post(url, data) {
        return send('post', `${host}${url}`, data, 'application/json');
    },
    delete(url) {
        return send('delete', `${host}${url}`);
    },
    upload(url, file) {
        return send('put', `${host}${url}`, file, file.type);
    },
    send(verb, url, data, additionalHeaders = {}) {
        return send(verb, url, data, 'application/json', additionalHeaders);
    },
};


const tinymceOptions = {
    object_resizing: true,
    relative_urls: false,
    image_advtab: true,
    remove_script_host: false,
    convert_urls: true,
    height: '600px',
    width: '100%',
    async image_list(success) {
        const files = await JinyaRequest.get('/api/media/file');
        success(files.items.map((item) => ({title: item.name, value: `${host}/${item.path}`})));
    },
    plugins: [
        'advlist',
        'anchor',
        'autolink',
        'charmap',
        'code',
        'colorpicker',
        'contextmenu',
        'fullscreen',
        'help',
        'hr',
        'image',
        'link',
        'lists',
        'media',
        'paste',
        'searchreplace',
        'table',
        'textcolor',
        'visualblocks',
        'wordcount',
    ],
    toolbar: 'undo redo | '
        + 'styleselect | '
        + 'bold italic | '
        + 'alignleft aligncenter alignright alignjustify | '
        + 'bullist numlist outdent indent | '
        + 'forecolor backcolor | '
        + 'link image | ',
    file_picker_type: 'image',
    file_picker_callback(cb) {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.onchange = async (event) => {
            const file = event.target.files[0];
            try {
                const {id} = await JinyaRequest.post('/api/media/file', {name: file.name});
                await JinyaRequest.put(`/api/media/file/${id}/content`);
                await JinyaRequest.upload(`/api/media/file/${id}/content/0`, file);
                await JinyaRequest.put(`/api/media/file/${id}/content/finish`);
                const uploadedFile = await JinyaRequest.get(`/api/media/file/${id}`);

                cb(`${host}${uploadedFile.path}`, {title: file.name});
            } catch (e) {
                if (e instanceof ConflictError) {
                    const files = await JinyaRequest.get(`/api/media/file?keyword=${encodeURIComponent(file.name)}`);
                    const selectedFile = files.items[0];

                    cb(`${host}${selectedFile.path}`, {title: selectedFile.name,});
                }
            }
        };

        input.click();
    },
}

function initTinyMce(id, startupContent) {
    tinymce.init({
        selector: `#${id}`,
        ...tinymceOptions,
        setup(editor) {
            editor.on('init', (e) => {
                editor.setContent(startupContent);
            });
        },
    });
}

function getContent(id) {
    return tinymce.get(id).getContent();
}

function setContent(id, content) {
    tinymce.get(id).setContent(content);
}

function destroyEditor(id) {
    tinymce.get(id).remove();
}
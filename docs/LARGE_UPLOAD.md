# Large file upload (e.g. 400MB video) and progress bar

## Backend: allow 400MB uploads

### 1. Laravel config

In `.env` set:

```env
MEDIA_MAX_UPLOAD_MB=400
```

Validation will allow files up to 400 MB.

### 2. PHP (php.ini)

Your server PHP must accept the request. Set at least:

```ini
upload_max_filesize = 512M
post_max_size = 512M
max_execution_time = 300
memory_limit = 256M
```

- `upload_max_filesize` / `post_max_size`: must be **greater than** your max video size (e.g. 512M for 400MB).
- `max_execution_time`: long enough for the upload to finish (seconds).

### 3. Web server

**Nginx** (in `server` or `http`):

```nginx
client_max_body_size 512M;
```

**Apache**: usually no change; limit is from PHP.

---

## Progress bar (frontend)

The backend does not send progress; it only receives the file. Progress is calculated on the client from **bytes sent**.

### Axios example (with progress)

```javascript
const file = document.querySelector('input[type="file"]').files[0];
const formData = new FormData();
formData.append('file', file);

await axios.post('/api/v1/user/media', formData, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data',
  },
  onUploadProgress(progressEvent) {
    const percent = progressEvent.total
      ? Math.round((progressEvent.loaded * 100) / progressEvent.total)
      : 0;
    console.log(`${percent}%`);
    // Update your progress bar UI:
    // progressBar.style.width = `${percent}%`;
  },
});
```

### Fetch API example (with progress)

```javascript
const file = document.querySelector('input[type="file"]').files[0];
const formData = new FormData();
formData.append('file', file);

const xhr = new XMLHttpRequest();

xhr.upload.addEventListener('progress', (e) => {
  if (e.lengthComputable) {
    const percent = Math.round((e.loaded * 100) / e.total);
    console.log(`${percent}%`);
    // progressBar.style.width = `${percent}%`;
  }
});

xhr.open('POST', '/api/v1/user/media');
xhr.setRequestHeader('Authorization', `Bearer ${token}`);
xhr.send(formData);
```

Use the **upload** progress event (`xhr.upload` or axios `onUploadProgress`); that reflects bytes sent to the server and is what you use for the progress bar.

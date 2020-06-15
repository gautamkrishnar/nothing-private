# Notes on Ungoogled chromium

After installation of the ungoogled chromium, you need to follow the following steps to make it secure from fingerprinting:

1. Open the ungoogled chromium app
2. Visit [chrome://flags](chrome://flags)
3. Enable the following flags:

| Flag | Value |
| ------------- | ------------- |
| Enable get*ClientRects() | Enabled |
| Enable Canvas::measureText() fingerprint deception | Enabled |
| Enable Canvas image data fingerprint deception | Enabled |

Since this version of chromium is free from google services you are less likely to be tracked.

![Ungoogled chromium](https://user-images.githubusercontent.com/8397274/77824280-865b1200-7127-11ea-85e7-0241346d2f4b.png)
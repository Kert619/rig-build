const MAX_RETRIES = 3;
const TIMEOUT = 60000; // 60 seconds

export async function loadWithRetry(page, url, retries = MAX_RETRIES) {
    for (let attempt = 1; attempt <= retries; attempt++) {
        try {
            const response = await page.goto(url, {
                waitUntil: "networkidle2",
                timeout: TIMEOUT,
            });
            return response;
        } catch (error) {
            if (attempt === retries) throw error;
            await new Promise((res) => setTimeout(res, 1000 * attempt)); // exponential backoff
        }
    }
}

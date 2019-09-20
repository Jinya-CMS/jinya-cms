import JinyaRequest from '@/framework/Ajax/JinyaRequest';
import Routes from '@/router/Routes';
import router from '@/router';
import {
    clearAuth,
    getApiKey,
    getDeviceCode,
    setApiKey,
    setCurrentUser,
    setCurrentUserRoles,
    setDeviceCode,
} from '@/framework/Storage/AuthStorage';

export async function refreshMe()
{
    if (getApiKey()) {
        const user = await JinyaRequest.get('/api/account');
        setCurrentUser(user);
        setCurrentUserRoles(user.roles);
    }
}

export async function logout()
{
    router.push(Routes.Account.Login);
    JinyaRequest.delete(`/api/account/api_key/${getApiKey()}`).then(() => {
    });

    clearAuth();
}

export async function requestTwoFactor(username, password)
{
    await JinyaRequest.post('/api/2fa', {
        username,
        password,
    });
}

export async function login(username, password, twoFactorCode)
{
    try {
        if (getDeviceCode() || twoFactorCode) {
            const headers = {};
            if (getDeviceCode()) {
                headers.JinyaDeviceCode = getDeviceCode();
            }

            const value = await JinyaRequest.send('POST', '/api/login', {
                username,
                password,
                twoFactorCode,
            }, headers);

            setApiKey(value.apiKey);
            setDeviceCode(value.deviceCode);

            await refreshMe();

            return true;
        }
        await requestTwoFactor(username, password);

        return false;
    } catch (e) {
        if (e.type === 'UnknownDeviceException') {
            clearAuth(true);
            await requestTwoFactor(username, password);

            return false;
        }
        throw e;
    }
}

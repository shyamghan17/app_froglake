import CriticalNoticeAlert from '../components/CriticalNoticeAlert';

export const generalAlert = () => {
    return [{
        id: 'critical-notice-alert',
        component: <CriticalNoticeAlert />,
    }];
};

import React, {FC} from 'react';
import {ConnectedApp} from '../../../model/Apps/connected-app';
import {useTranslate} from '../../../shared/translate';
import styled from 'styled-components';
import {DangerIcon, getColor, getFontSize} from 'akeneo-design-system';

const Error = styled.div`
    color: ${getColor('red', 100)};
    font-size: ${getFontSize('small')};
    font-weight: normal;
    margin: 0;
    margin-bottom: 5px;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; ;
`;

const ErrorIcon = styled(DangerIcon)`
    color: ${getColor('red', 100)};
    vertical-align: middle;
    margin-right: 5px;
`;

const Tag = styled.span`
    color: ${getColor('grey', 120)};
    font-size: ${getFontSize('small')};
    text-transform: uppercase;
    font-weight: normal;

    border: 1px ${getColor('grey', 120)} solid;
    background: ${getColor('white')};
    border-radius: 2px;

    display: inline-block;
    line-height: ${getFontSize('small')};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    padding: 2px 5px;
    margin-right: 5px;
`;

const Author = styled.div`
    color: ${getColor('grey', 120)};
    font-size: ${getFontSize('big')};
    font-weight: normal;
    margin: 0;
    margin-bottom: 5px;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
`;

type Props = {
    connectedApp: ConnectedApp;
};

const ConnectedAppCardDescription: FC<Props> = ({connectedApp}) => {
    const translate = useTranslate();
    const author =
        connectedApp.author ??
        translate('akeneo_connectivity.connection.connect.connected_apps.list.test_apps.removed_user');

    if (true !== connectedApp.is_loaded) {
        return null;
    }

    if (false === connectedApp.is_listed_on_the_appstore) {
        const message = translate(
            'akeneo_connectivity.connection.connect.connected_apps.list.card.not_listed_on_the_appstore'
        );

        return (
            <Error title={message}>
                <ErrorIcon size={14} />
                {message}
            </Error>
        );
    }

    return (
        <>
            <Author>
                {translate('akeneo_connectivity.connection.connect.connected_apps.list.card.developed_by', {
                    author,
                })}
            </Author>
            {connectedApp.categories.length > 0 && <Tag>{connectedApp.categories[0]}</Tag>}
        </>
    );
};

export default ConnectedAppCardDescription;

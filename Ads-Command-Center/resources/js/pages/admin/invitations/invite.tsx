import { Head, Form } from '@inertiajs/react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/invitations';

export default function Invite() {
    return (
        <>
            <Head title="Invite specialist" />

            <Form
                {...store.form()}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    name="email"
                                    placeholder="specialist@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                data-test="invite-specialist-button"
                            >
                                {processing && <Spinner />}
                                Send invite
                            </Button>
                        </div>

                        <div className="text-muted-foreground text-center text-sm">
                            Back to{' '}
                            <TextLink href="/dashboard">dashboard</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </>
    );
}

Invite.layout = {
    title: 'Invite specialist',
    description: 'Invite a specialist to join your organization',
};
